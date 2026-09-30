@extends('layouts.chat')

@section('content')
<div class="main-container">
    <!-- Left Column (Videos) -->
    <div class="col-video">
        <!-- Remote Video -->
        <div class="remote-video-container rounded">
            <video id="remoteVideo" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
            <div id="statusText" class="position-absolute text-white" style="z-index: 5; font-size: 1.2rem;">Menunggu koneksi...</div>
            <div class="position-absolute text-white-50" style="bottom: 10px; left: 15px; font-weight: bold;">
            </div>
        </div>
        
        <!-- Local Video -->
        <div class="local-video-container rounded">
            <video id="localVideo" autoplay playsinline muted style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
        </div>
    </div>

    <!-- Right Column (Chat) -->
    <div class="col-chat">
        
        <!-- Chat Messages Area -->
        <div id="chatBox" style="flex: 1; overflow-y: auto; padding: 20px;  font-size: 15px;">
            
            <!-- Messages go here -->
        </div>

        <!-- Chat Controls Area -->
        <div style="padding: 10px 15px; border-top: 1px solid #eee;">
            
            
            <div style="display: flex; gap: 10px;">
                <button id="btnStart" class="btn" style="background: #5864FF; color: white; border-radius: 8px; width: 80px; font-weight: bold; flex-shrink: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; line-height: 1.2;">
                    Start<br><span style="font-size: 11px; font-weight: normal;">Esc</span>
                </button>
                <button id="btnNext" class="btn d-none" style="background: #5864FF; color: white; border-radius: 8px; width: 80px; font-weight: bold; flex-shrink: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; line-height: 1.2;">
        Next<br><span style="font-size: 11px; font-weight: normal;">Esc</span>
    </button>
                
                <div style="flex: 1; position: relative;">
                    <input type="text" id="chatInput" class="form-control" placeholder="Type a message..." disabled style="height: 100%; border-radius: 8px; font-size: 16px; padding-right: 50px;">
                    <button id="btnSend" class="btn" disabled style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #aaa;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                          <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const localVideo = document.getElementById('localVideo');
        const remoteVideo = document.getElementById('remoteVideo');
        const btnStart = document.getElementById('btnStart');
        const btnNext = document.getElementById('btnNext');
        const statusText = document.getElementById('statusText');
        const chatInput = document.getElementById('chatInput');
        const btnSend = document.getElementById('btnSend');
        const chatBox = document.getElementById('chatBox');
        
        let localStream;
        let peerConnection;
        let currentChannel;
        let myRole;
        let myUserId = Math.random().toString(36).substring(2, 15);
        let echoChannel;
        
        const servers = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' }
            ]
        };

        // Mulai kamera saat halaman dimuat
        navigator.mediaDevices.getUserMedia({ video: true, audio: true })
            .then(stream => {
                localStream = stream;
                localVideo.srcObject = stream;
                console.log("Kamera diizinkan.");
            })
            .catch(error => {
                console.error("Kamera/Mic tidak diizinkan atau tidak ditemukan.", error);
                // Tidak masalah, kita tetap bisa text chat
            });

        // Tombol dimatikan dulu sampai koneksi WebSocket siap
        btnStart.disabled = true;

        // Tunggu sampai Echo diinisiasi oleh app.js (terutama untuk HP)
        let echoCheckInterval = setInterval(() => {
            if(window.Echo) {
                clearInterval(echoCheckInterval);
                
                // Aktifkan tombol mulai jika WebSocket sudah konek
                btnStart.disabled = false;
                
                console.log("Menghubungkan Echo ke user channel...");
                window.Echo.channel('user.' + myUserId)
                    .listen('.PartnerFound', (e) => {
                        console.log("Matched!", e);
                        statusText.innerText = "Menghubungkan...";
                        myRole = e.role;
                        currentChannel = e.channelName;
                        joinChatChannel();
                    });
            }
        }, 500);

        btnStart.addEventListener('click', () => {
            btnStart.classList.add('d-none'); btnNext.classList.remove('d-none'); btnNext.disabled = false;
            statusText.innerText = "Mencari teman...";
            
            fetch('/match', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ user_id: myUserId })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'matched') {
                    myRole = data.role;
                    currentChannel = data.channel;
                    statusText.innerText = "Teman ditemukan!";
                    joinChatChannel();
                } else {
                    console.log("Waiting in queue...");
                }
            })
            .catch(err => {
                console.error(err);
                statusText.innerText = "Terjadi kesalahan server.";
                btnStart.disabled = false;
            });
        });
        let iceCandidateQueue = [];

        function joinChatChannel() {
            if(!window.Echo) {
                console.error("Echo belum siap.");
                return;
            }
            echoChannel = window.Echo.channel(currentChannel);
            
            setupPeerConnection();
            
            echoChannel.listen('.SignalSent', (e) => {
                if(e.from === myUserId) return;
                
                const signal = e.signal;
                if(signal && signal.sdp && !signal.sdp.endsWith("\r\n")) signal.sdp += "\r\n";
                
                if(signal.type === 'offer') {
                    peerConnection.setRemoteDescription(new RTCSessionDescription(signal))
                        .then(() => {
                            flushIceQueue();
                            return peerConnection.createAnswer();
                        })
                        .then(answer => peerConnection.setLocalDescription(answer))
                        .then(() => sendSignal(peerConnection.localDescription))
                        .catch(err => alert("Error saat menerima panggilan (Offer): " + err));
                } else if(signal.type === 'answer') {
                    peerConnection.setRemoteDescription(new RTCSessionDescription(signal))
                        .then(() => flushIceQueue())
                        .catch(err => alert("Error saat menerima jawaban (Answer): " + err));
                } else if(signal.candidate) {
                    if (peerConnection.remoteDescription) {
                        peerConnection.addIceCandidate(new RTCIceCandidate(signal));
                    } else {
                        iceCandidateQueue.push(signal);
                    }
                } else if(signal.type === 'chat') {
                    appendMessage("Teman", signal.message);
                }
            });
            
            if(myRole === 'caller') {
                setTimeout(() => {
                    peerConnection.createOffer()
                        .then(offer => peerConnection.setLocalDescription(offer))
                        .then(() => sendSignal(peerConnection.localDescription))
                        .catch(err => alert("Error saat membuat panggilan (Create Offer): " + err));
                }, 2000);
            }
            
            chatInput.disabled = false;
            btnSend.disabled = false;
        }

        function flushIceQueue() {
            while(iceCandidateQueue.length > 0) {
                let candidate = iceCandidateQueue.shift();
                peerConnection.addIceCandidate(new RTCIceCandidate(candidate)).catch(e => console.error(e));
            }
        }

        function setupPeerConnection() {
            peerConnection = new RTCPeerConnection(servers);
            
            if(localStream) {
                localStream.getTracks().forEach(track => {
                    peerConnection.addTrack(track, localStream);
                });
            }
            
            peerConnection.ontrack = event => {
                remoteVideo.srcObject = event.streams[0];
                statusText.style.display = 'none';
            };
            
            peerConnection.onicecandidate = event => {
                if(event.candidate) sendSignal(event.candidate);
            };
            
            peerConnection.oniceconnectionstatechange = () => {
                if(peerConnection.iceConnectionState === 'disconnected') {
                    statusText.style.display = 'block';
                    statusText.innerText = "Teman telah pergi.";
                    remoteVideo.srcObject = null;
                }
            };
        }

        function sendSignal(signalData) {
            fetch('/signal', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    channel: currentChannel,
                    signal: signalData,
                    from: myUserId
                })
            }).then(res => {
                if(!res.ok) alert("Gagal mengirim sinyal HTTP " + res.status);
            }).catch(err => alert("Network error: " + err));
        }
        
        btnNext.addEventListener('click', () => {
            location.reload();
        });
        
        btnSend.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', (e) => {
            if(e.key === 'Enter') sendMessage();
        });
        
        function sendMessage() {
            const msg = chatInput.value;
            if(!msg) return;
            
            appendMessage("Anda", msg);
            sendSignal({ type: 'chat', message: msg });
            chatInput.value = '';
        }
        
        function appendMessage(sender, msg) {
            const el = document.createElement('div');
            el.innerHTML = `<strong>${sender}:</strong> ${msg}`;
            chatBox.appendChild(el);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>
@endpush


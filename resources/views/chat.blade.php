<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YuhChat - Live Video Chat</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- YuhTv Landing CSS -->
    <link rel="stylesheet" href="style.css">
    
    <!-- App Scripts (Echo) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        .d-none { display: none !important; }
                body, html {
            height: 100%;
            margin: 0;
            background-color: #f8f9fa;
            overflow: hidden; /* Prevent scrolling, true OmeTV feel */
            display: flex;
            flex-direction: column;
            font-family: 'Inter', sans-serif;
        }
        .main-container {
            flex: 1;
            width: 100%;
            display: flex;
            padding: 15px;
            gap: 15px;
            height: 0; /* Important for flex child with overflow */
        }
        
        .col-video { flex: 7; }
        .col-chat {
            flex: 3;
            display: flex;
            flex-direction: column;
            background: #fff;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-radius: 12px;
            overflow: hidden;
        }

        
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
                padding: 10px;
                gap: 10px;
            }
            .col-video {
                flex: 0 0 60%;
            }
            .col-chat {
                flex: 1;
            }
            .local-video-container {
                width: 80px;
                height: 112px;
                bottom: 10px;
                left: 10px;
            }
            
            
        }
        .action-buttons-container {
            display: flex;
            gap: 10px;
        }
        @media (max-width: 768px) {
            .action-buttons-container {
                flex-wrap: wrap;
            }
            .action-buttons-container > * {
                flex: 1 1 45% !important; /* Forces wrapping into 2 rows of 2 on mobile */
                font-size: 14px;
                padding: 8px 4px; /* Reduce padding to fit */
            }
            
            /* Fix col-video flex on mobile so it doesn't overflow */
            .col-video {
                flex: 0 0 55%; /* Slightly reduce video height to make room for 2 rows of buttons */
            }
            .col-chat {
                flex: 1;
                /* Add smooth scrolling to chat on mobile */
                -webkit-overflow-scrolling: touch; 
            }
            
            /* Hide the text label of country to save space, keep flag */
            .country-text {
                display: none;
            }
        }

    </style>
  </head>
  <body>
    <!-- Top Bar -->
        

    <div class="main-container">
    <!-- Left Column (Videos) -->
        <div class="col-video" style="display: flex; flex-direction: column; gap: 10px;">
        
        <!-- Video Area -->
        <div style="flex: 1; position: relative; overflow: hidden; border-radius: 8px; background: #444;">
            <div class="remote-video-container" style="width: 100%; height: 100%;">
                <video id="remoteVideo" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                <div id="statusText" style="position: absolute; color: white; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 5; font-size: 1.2rem;">Click Start to find a partner</div>
            </div>
            
            <div class="local-video-container" style="position: absolute; bottom: 15px; left: 15px; width: 25%; aspect-ratio: 4/3; overflow: hidden; background-color: #333; border: 2px solid white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.5); z-index: 10;">
                <video id="localVideo" autoplay playsinline muted style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
            </div>
        </div>

                <!-- Buttons Area -->
        <div class="action-buttons-container">
            <button id="btnStart" class="btn-acid" style="flex: 1; border-radius: 8px; justify-content: center;">Start</button>
            <button id="btnNext" class="btn-acid d-none" style="flex: 1; border-radius: 8px; justify-content: center;">Next</button>
            <button id="btnStop" class="btn-ghost" style="flex: 1; border-radius: 8px; justify-content: center;">Stop</button>
            <div class="btn-ghost" style="flex: 1; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 600; cursor: default; user-select: none; gap: 6px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="14" r="5"></circle><line x1="13.54" y1="10.46" x2="20" y2="4"></line><polyline points="15 4 20 4 20 9"></polyline></svg>
                Male
            </div>
            <div class="btn-ghost" style="flex: 1; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 600; cursor: default; user-select: none; gap: 6px;">
                🇮🇩 <span class="country-text">Indonesia</span>
            </div>
        </div>

    </div>

    <!-- Right Column (Chat) -->
    <div class="col-chat">
        
        <!-- Chat Messages Area -->
        <div id="chatBox" style="flex: 1; overflow-y: auto; padding: 20px; font-size: 15px;">
        </div>

        <!-- Chat Controls Area -->
        <div style="padding: 10px; border-top: 1px solid #ddd;">
            <div style="position: relative;">
                <input type="text" id="chatInput" class="form-control" placeholder="Send message..." disabled style="width: 100%; padding-left: 10px; box-sizing: border-box; height: 45px; border: 1px solid rgba(0,0,0,0.15); border-radius: 8px; font-size: 16px; padding-right: 50px; font-weight: 500;">
                <button id="btnSend" class="btn" disabled style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #b8de00;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>


    <!-- ======== JS here ======== -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const localVideo = document.getElementById('localVideo');
        const remoteVideo = document.getElementById('remoteVideo');
        const btnStart = document.getElementById('btnStart');
        const btnNext = document.getElementById('btnNext');
        const btnStop = document.getElementById('btnStop');
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
            });

        btnStart.disabled = true;
        btnStop.disabled = true;

        let echoCheckInterval = setInterval(() => {
            if(window.Echo) {
                clearInterval(echoCheckInterval);
                btnStart.disabled = false;
                
                window.Echo.channel('user.' + myUserId)
                    .listen('.PartnerFound', (e) => {
                        console.log("Matched!", e);
                        statusText.innerText = "Connecting...";
                        myRole = e.role;
                        currentChannel = e.channelName;
                        joinChatChannel();
                    });
            }
        }, 500);

        function stopChat(skipLeaveSignal = false) {
            if(!skipLeaveSignal && currentChannel) {
                sendSignal({ type: 'leave' });
            }
            if(peerConnection) {
                peerConnection.close();
                peerConnection = null;
            }
            if(echoChannel && currentChannel) {
                window.Echo.leave(currentChannel);
                echoChannel = null;
            }
            currentChannel = null;
            remoteVideo.srcObject = null;
            chatBox.innerHTML = '';
            statusText.style.display = 'block';
            statusText.innerText = "Click Start to find a partner";
            
            btnStart.classList.remove('d-none');
            btnNext.classList.add('d-none');
            
            btnStop.disabled = true;
            chatInput.disabled = true;
            btnSend.disabled = true;
        }

        function startSearch() {
            btnStart.classList.add('d-none'); 
            btnNext.classList.remove('d-none'); 
            btnNext.disabled = false;
            btnStop.disabled = false;
            chatBox.innerHTML = '';
            
            statusText.style.display = 'block';
            statusText.innerText = "Searching for a partner...";
            
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
                    statusText.innerText = "Partner found!";
                    joinChatChannel();
                } else {
                    console.log("Waiting in queue...");
                }
            })
            .catch(err => {
                console.error(err);
                statusText.innerText = "Server error occurred.";
                stopChat();
            });
        }

        btnStart.addEventListener('click', startSearch);
        
        btnNext.addEventListener('click', () => {
            stopChat(false);
            setTimeout(startSearch, 50); // Inisiator langsung mencari
        });
        
        btnStop.addEventListener('click', () => {
            stopChat(false);
        });

        let iceCandidateQueue = [];

        function joinChatChannel() {
            if(!window.Echo) return;
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
                    appendMessage("Stranger", signal.message);
                } else if(signal.type === 'leave') {
                    console.log("Partner left, auto searching...");
                    stopChat(true);
                    setTimeout(startSearch, 1500); // Receiver menunggu 1.5 detik agar tidak crash bareng
                }
            });
            
            if(myRole === 'caller') {
                setTimeout(() => {
                    peerConnection.createOffer()
                        .then(offer => peerConnection.setLocalDescription(offer))
                        .then(() => sendSignal(peerConnection.localDescription))
                        .catch(err => alert("Error saat membuat panggilan (Create Offer): " + err));
                }, 100);
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
                if(peerConnection && (peerConnection.iceConnectionState === 'disconnected' || peerConnection.iceConnectionState === 'failed')) {
                    console.log("Partner disconnected, auto searching...");
                    stopChat(true);
                    setTimeout(startSearch, 800 + Math.random() * 1000); // Random jitter
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
        
        btnSend.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', (e) => {
            if(e.key === 'Enter') sendMessage();
        });
        
        function sendMessage() {
            const msg = chatInput.value;
            if(!msg) return;
            
            appendMessage("You", msg);
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

  </body>
</html>

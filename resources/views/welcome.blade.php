@extends('layouts.app')

@section('content')
<!-- ======== hero-section start ======== -->
    <section id="home" class="hero-section">
      <div class="container">
        <div class="row align-items-center position-relative">
          <div class="col-lg-6">
            <div class="hero-content">
              <h1 class="wow fadeInUp" data-wow-delay=".4s">
                Temukan Teman Baru secara Acak dengan YuhChat
              </h1>
              <p class="wow fadeInUp" data-wow-delay=".6s">
                Platform video dan text chat acak gratis. Temukan teman ngobrol, bertukar cerita, dan berinteraksi secara real-time langsung dari browser Anda tanpa ribet.
              </p>
              <a
                href="{{ url('/chat') }}"
                class="main-btn border-btn btn-hover wow fadeInUp"
                data-wow-delay=".6s"
                >Mulai Sekarang!</a
              >
              <a href="#features" class="scroll-bottom">
                <i class="lni lni-arrow-down"></i
              ></a>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="hero-img wow fadeInUp" data-wow-delay=".5s">
              <img src="{{ asset('assets/img/hero/hero-img.png') }}" alt="" />
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- ======== hero-section end ======== -->


    
@endsection

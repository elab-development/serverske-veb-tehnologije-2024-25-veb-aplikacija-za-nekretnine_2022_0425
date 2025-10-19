@extends('frontend.frontend_dashboard')
@section('main')
@section('title')
Contact Us | Easy RealEstate  
@endsection

<!--Page Title-->
<section class="page-title-two bg-color-1 centred">
    <div class="pattern-layer">
        <div class="pattern-1" style="background-image: url({{ asset('frontend/assets/images/shape/shape-9.png') }});"></div>
        <div class="pattern-2" style="background-image: url({{ asset('frontend/assets/images/shape/shape-10.png') }});"></div>
    </div>
    <div class="auto-container">
        <div class="content-box clearfix">
            <h1>Contact Us</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li>Contact Us</li>
            </ul>
        </div>
    </div>
</section>
<!--End Page Title-->

<!-- contact-section -->
<section class="contact-section sec-pad">
    <div class="auto-container">
        <div class="row clearfix">
            <div class="col-lg-4 col-md-12 col-sm-12 content-column">
                <div class="content_block_3">
                    <div class="content-box">
                        <div class="sec-title">
                            <h5>Contact Info</h5>
                            <h2>Get In Touch</h2>
                        </div>
                        <div class="inner-box">
                            <div class="single-item">
                                <div class="icon-box"><i class="icon-179"></i></div>
                                <ul>
                                    <li><h5>Phone Number</h5></li>
                                    <li><a href="tel:+1234567890">+1 234 567 890</a></li>
                                    <li><a href="tel:+1234567891">+1 234 567 891</a></li>
                                </ul>
                            </div>
                            <div class="single-item">
                                <div class="icon-box"><i class="icon-180"></i></div>
                                <ul>
                                    <li><h5>Email Address</h5></li>
                                    <li><a href="mailto:info@example.com">info@example.com</a></li>
                                    <li><a href="mailto:support@example.com">support@example.com</a></li>
                                </ul>
                            </div>
                            <div class="single-item">
                                <div class="icon-box"><i class="icon-181"></i></div>
                                <ul>
                                    <li><h5>Office Address</h5></li>
                                    <li>123 Main Street<br />New York, NY 10001</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-12 col-sm-12 form-column">
                <div class="form-inner">
                    <div class="sec-title">
                        <h5>Get In Touch</h5>
                        <h2>Ready to Get Started?</h2>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="post" action="{{ route('store.contact') }}" class="default-form">
                        @csrf
                        <div class="row clearfix">
                            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                <input type="text" name="name" placeholder="Your Name" required="" value="{{ old('name') }}">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                <input type="email" name="email" placeholder="Email Address" required="" value="{{ old('email') }}">
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                <input type="text" name="subject" placeholder="Subject" required="" value="{{ old('subject') }}">
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                <textarea name="message" placeholder="Message" required="">{{ old('message') }}</textarea>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 form-group message-btn">
                                <button type="submit" class="theme-btn btn-one">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- contact-section end -->

<!-- google-map-section -->
<section class="google-map-section">
    <div class="map-inner">
        <div id="contact-google-map" class="google-map" 
             style="height: 400px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
            <div style="text-align: center; color: #666;">
                <i class="fas fa-map-marker-alt" style="font-size: 48px; margin-bottom: 10px;"></i>
                <p>Interactive Map Coming Soon</p>
                <p><small>123 Main Street, New York, NY 10001</small></p>
            </div>
        </div>
    </div>
</section>
<!-- google-map-section end -->

@endsection
@extends('frontend.frontend_dashboard')
@section('main')

<!--Page Title-->
<section class="page-title-two bg-color-1 centred">
    <div class="pattern-layer">
        <div class="pattern-1" style="background-image: url({{ asset('frontend/assets/images/shape/shape-9.png') }});"></div>
        <div class="pattern-2" style="background-image: url({{ asset('frontend/assets/images/shape/shape-10.png') }});"></div>
    </div>
    <div class="auto-container">
        <div class="content-box clearfix">
            <h1>All Agents</h1>
            <ul class="bread-crumb clearfix">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li>All Agents</li>
            </ul>
        </div>
    </div>
</section>
<!--End Page Title-->

<!-- team-section -->
<section class="team-section bg-color-1">
    <div class="auto-container">
        <div class="row clearfix">
            @foreach($agents as $agent)
            <div class="col-lg-4 col-md-6 col-sm-12 team-block">
                <div class="team-block-one wow fadeInUp animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                    <div class="inner-box">
                        <figure class="image-box">
                            <img src="{{ (!empty($agent->photo)) ? url('upload/agent_images/'.$agent->photo) : url('upload/no_image.jpg') }}" alt="" style="width:370px; height:370px;">
                        </figure>
                        <div class="lower-content">
                            <div class="inner">
                                <h4><a href="{{ route('agent.details', $agent->id) }}">{{ $agent->name }}</a></h4>
                                <span class="designation">{{ $agent->username ?? 'Real Estate Agent' }}</span>
                                <ul class="social-links clearfix">
                                    @if($agent->facebook)
                                    <li><a href="{{ $agent->facebook }}"><i class="fab fa-facebook-f"></i></a></li>
                                    @endif
                                    @if($agent->twitter)
                                    <li><a href="{{ $agent->twitter }}"><i class="fab fa-twitter"></i></a></li>
                                    @endif
                                    <li><a href="mailto:{{ $agent->email }}"><i class="fas fa-envelope"></i></a></li>
                                    @if($agent->phone)
                                    <li><a href="tel:{{ $agent->phone }}"><i class="fas fa-phone"></i></a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="pagination-wrapper text-center">
            {{ $agents->links('vendor.pagination.custom') }}
        </div>
    </div>
</section>
<!-- team-section end -->

<!-- subscribe-section -->
<section class="subscribe-section bg-color-3">
    <div class="pattern-layer" style="background-image: url({{ asset('frontend/assets/images/shape/shape-2.png') }});"></div>
    <div class="auto-container">
        <div class="row clearfix">
            <div class="col-lg-6 col-md-6 col-sm-12 text-column">
                <div class="text">
                    <span>Subscribe</span>
                    <h2>Sign Up To Our Newsletter To Get The Latest News And Offers.</h2>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 form-column">
                <div class="form-inner">
                    <form action="contact.html" method="post" class="subscribe-form">
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Enter your email" required="">
                            <button type="submit">Subscribe Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- subscribe-section end -->

@endsection
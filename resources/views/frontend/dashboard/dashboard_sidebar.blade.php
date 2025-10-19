 <div class="widget-content">
                <ul class="category-list ">
                   
    <li class="current">  <a href="blog-details.html"><i class="fab fa fa-envelope "></i> Dashboard </a></li>

    {{-- Show Switch Back to Agent if user switched from agent --}}
    @if(session('switched_from_agent'))
    <li><a href="{{ route('user.switch.back.agent') }}" style="background-color: #f0f8ff; border-left: 4px solid #007bff;"><i class="fa fa-exchange" aria-hidden="true"></i> <strong>Switch Back to Agent</strong></a></li>
    @endif

    <li><a href="{{ route('user.profile') }}"><i class="fa fa-cog" aria-hidden="true"></i> Settings</a></li>
    <li><a href="{{ route('user.schedule.request') }}"><i class="fa fa-credit-card" aria-hidden="true"></i>Schedule Request <span class="badge badge-info">(  )</span></a></li>
    <li><a href="{{ route('user.compare') }}"><i class="fa fa-list-alt" aria-hidden="true"></i></i> Compare </a></li>
    <li><a href="{{ route('user.wishlist') }}"><i class="fa fa-indent" aria-hidden="true"></i> WishList  </a></li>
    <li><a href="{{ route('live.chat') }}"><i class="fa fa-indent" aria-hidden="true"></i> Live Chat  </a></li>
    <li><a href="{{ route('user.change.password') }}"><i class="fa fa-key" aria-hidden="true"></i> Security </a></li>
    <li><a href="{{ route('user.logout') }}"><i class="fa fa-chevron-circle-up" aria-hidden="true"></i> Logout </a></li>
                </ul>
            </div>
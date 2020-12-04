@extends('layouts.app')

@section('content')
    <section class="section">

        <div class="section__content section__content_center">
            <div class="wrapper">
                <div id="animation" class="main-animation"></div>
                <p class="section__text">{{ __('Have fun, find love,or even date a celebrity!') }}</p>
                <a href="#" class="signup">Sign up</a>
                <div id="people" class="people"></div>
            </div>
        </div>
        <div class="section__footer">
            <p>There are most successful users of your region</p>
        </div>
    </section>

    <section class="section section_top">
        <div class="wrapper">
            <div class="section__header">
                <div class="section__title">Top <img src="img/icon/crown.svg" class="section__title-img" alt=""> Folk
                </div>
                <div class="section__subtitle">of <span>February</span></div>
            </div>
            <div class="users">
                <div class="user user_large">
                    <div class="user__info">
                        <div class="user__img"
                             data-avatar="https://images.unsplash.com/photo-1496345875659-11f7dd282d1d?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1350&q=80">
                        </div>
                        <div class="user__profile">
                            <div class="user__name">Raisociyante</div>
                            <div class="user__age">age: 23</div>
                            <a href="#" class="buy">buy a date</a>
                        </div>
                    </div>
                    <div class="user__subscribers">3.1k</div>
                </div>
                <div class="user user_large">
                    <div class="user__info">
                        <div class="user__img"
                             data-avatar="https://images.unsplash.com/photo-1492567291473-fe3dfc175b45?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=631&q=80">
                        </div>
                        <div class="user__profile">
                            <div class="user__name">Raisociyante</div>
                            <div class="user__age">age: 23</div>
                            <a href="#" class="buy">buy a date</a>
                        </div>
                    </div>
                    <div class="user__subscribers">4.6k</div>
                </div>
                <div class="user user_large">
                    <div class="user__info">
                        <div class="user__img"
                             data-avatar="https://images.unsplash.com/photo-1528977695568-bd5e5069eb61?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1500&q=80">
                        </div>
                        <div class="user__profile">
                            <div class="user__name">Raisociyante</div>
                            <div class="user__age">age: 23</div>
                            <a href="#" class="buy">buy a date</a>
                        </div>
                    </div>
                    <div class="user__subscribers">8.9k</div>
                </div>
                <div class="user user_large">
                    <div class="user__info">
                        <div class="user__img"
                             data-avatar="https://images.unsplash.com/photo-1506919258185-6078bba55d2a?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1030&q=80">
                        </div>
                        <div class="user__profile">
                            <div class="user__name">Raisociyante</div>
                            <div class="user__age">age: 23</div>
                            <a href="#" class="buy">buy a date</a>
                        </div>
                    </div>
                    <div class="user__subscribers">12k</div>
                </div>
                <div class="user user_large">
                    <div class="user__info">
                        <div class="user__img"
                             data-avatar="https://images.unsplash.com/photo-1464746133101-a2c3f88e0dd9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1454&q=80">
                        </div>
                        <div class="user__profile">
                            <div class="user__name">Raisociyante</div>
                            <div class="user__age">age: 23</div>
                            <a href="#" class="buy">buy a date</a>
                        </div>
                    </div>
                    <div class="user__subscribers">657</div>
                </div>
                <div class="user user_large">
                    <div class="user__info">
                        <div class="user__img"
                             data-avatar="https://images.unsplash.com/photo-1517480625158-292a09aee755?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=668&q=80">
                        </div>
                        <div class="user__profile">
                            <div class="user__name">Raisociyante</div>
                            <div class="user__age">age: 23</div>
                            <a href="#" class="buy">buy a date</a>
                        </div>
                    </div>
                    <div class="user__subscribers">891</div>
                </div>
                <div class="user user_large">
                    <div class="user__info">
                        <div class="user__img"
                             data-avatar="https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=668&q=80">
                        </div>
                        <div class="user__profile">
                            <div class="user__name">Raisociyante</div>
                            <div class="user__age">age: 23</div>
                            <a href="#" class="buy">buy a date</a>
                        </div>
                    </div>
                    <div class="user__subscribers">1.3k</div>
                </div>
                <div class="user user_large">
                    <div class="user__info">
                        <div class="user__img"
                             data-avatar="https://images.unsplash.com/photo-1500917293891-ef795e70e1f6?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1500&q=80">
                        </div>
                        <div class="user__profile">
                            <div class="user__name">Raisociyante</div>
                            <div class="user__age">age: 23</div>
                            <a href="#" class="buy">buy a date</a>
                        </div>
                    </div>
                    <div class="user__subscribers">2.3k</div>
                </div>
            </div>
        </div>
        <div class="section__footer">
            <p>Oodles of people thirst to date these region celebs</p>
        </div>
    </section>

    <section class="section">
        <div class="wrapper">
            <div class="section__header">
                <div class="section__title">Hot <img src="img/icon/flame.svg" class="section__title-img" alt="">
                    Auctions
                </div>
                <div class="section__subtitle section__subtitle_region">of your region</div>
            </div>
            <div class="section__content">
                <div class="hot-auction">
                    <div class="hot-auction__user">
                        <div class="hot-auction__user-avatar"
                             data-avatar="https://images.unsplash.com/photo-1515486191131-efd6be9f711f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1500&q=80">
                        </div>
                        <div class="hot-auction__user-lastbet">
                            <div class="last-bet__info">
                                <div class="last-bet__price">90.00</div>
                                <p class="last-bet__title">last bet</p>
                            </div>
                            <div class="last-bet__line"></div>
                            <div class="hot-auction__subscriber" data-count="150">345</div>
                        </div>
                        <div class="hot-auction__time-left">01:45:58:30</div>

                        <a href="#" class="hot-auction__link ">Join</a>

                        <span class="user__line"></span>
                    </div>
                    <div class="hot-auction__user">
                        <div class="hot-auction__user-avatar"
                             data-avatar="https://images.unsplash.com/photo-1515486191131-efd6be9f711f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1500&q=80">
                        </div>
                        <div class="hot-auction__user-lastbet">
                            <div class="last-bet__info">
                                <div class="last-bet__price">90.00</div>
                                <p class="last-bet__title">last bet</p>
                            </div>
                            <div class="last-bet__line"></div>
                            <div class="hot-auction__subscriber" data-count="250">233</div>
                        </div>
                        <div class="hot-auction__time-left">01:45:58:30</div>

                        <a href="#" class="hot-auction__link ">Join</a>

                        <span class="user__line"></span>
                    </div>
                    <div class="hot-auction__user">
                        <div class="hot-auction__user-avatar"
                             data-avatar="https://images.unsplash.com/photo-1515486191131-efd6be9f711f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1500&q=80">
                        </div>
                        <div class="hot-auction__user-lastbet">
                            <div class="last-bet__info">
                                <div class="last-bet__price">90.00</div>
                                <p class="last-bet__title">last bet</p>
                            </div>
                            <div class="last-bet__line"></div>
                            <div class="hot-auction__subscriber" data-count="350">423</div>
                        </div>
                        <div class="hot-auction__time-left">01:45:58:30</div>

                        <a href="#" class="hot-auction__link ">Join</a>

                        <span class="user__line"></span>
                    </div>
                    <div class="hot-auction__user">
                        <div class="hot-auction__user-avatar"
                             data-avatar="https://images.unsplash.com/photo-1515486191131-efd6be9f711f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1500&q=80">
                        </div>
                        <div class="hot-auction__user-lastbet">
                            <div class="last-bet__info">
                                <div class="last-bet__price">90.00</div>
                                <p class="last-bet__title">last bet</p>
                            </div>
                            <div class="last-bet__line"></div>
                            <div class="hot-auction__subscriber" data-count="300">324</div>
                        </div>
                        <div class="hot-auction__time-left">01:45:58:30</div>

                        <a href="#" class="hot-auction__link">Join</a>

                        <span class="user__line"></span>
                    </div>

                </div>
            </div>
        </div>
    </section>
    @push('scripts')

        <script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/2.0.2/TweenMax.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/2.0.2/TimelineMax.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.6/ScrollMagic.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.6/plugins/debug.addIndicators.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.6/plugins/animation.gsap.min.js"></script>
        <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
        <script src="{{ asset('js/lottie.min.js') }}"></script>
        <script src="{{ asset('js/common.js') }}"></script>
        <script src="{{ asset('js/animations.js') }}"></script>

    @endpush

@endsection


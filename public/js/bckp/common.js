'use strict';

var langOpen = {
    init: function () {
        var _this = this;
        _this.html = {
            $current: $('.lang__current'),
            $list: $('.lang__list'),

        };
        _this.classes = {
            currentShow: 'lang__current_show',
            hidden: 'lang__list_hidden',
            visible: 'lang__list_visible'
        };
        _this.event();
    },
    event: function () {
        var _this = this;
        _this.html.$current.on('click', function () {
            $(this).toggleClass(_this.classes.currentShow);
            if ($(this).siblings(_this.html.$list).hasClass(_this.classes.hidden)) {
                $(this).siblings(_this.html.$list).removeClass(_this.classes.hidden);
                $(this).siblings(_this.html.$list).addClass(_this.classes.visible);
            } else {
                $(this).siblings(_this.html.$list).addClass(_this.classes.hidden);
                $(this).siblings(_this.html.$list).removeClass(_this.classes.visible);
            }
        });

    }
};


$(document).ready(function () {

    langOpen.init();


    $(document).mouseup(function (e) {
        var user = $('.user');
        if (!user.is(e.target) && user.has(e.target).length == 0) {
            $('.section_top').removeClass('section_disable');
            $('.user').removeClass('user_show');
            $('.user').removeClass('user_show-left');
        }
    });
    // Avatar
    $('[data-avatar]').each(function () {
        var src = $(this).data('avatar');
        $(this).css({
            'background-image': 'url(' + src + ')'
        });
        $(this).removeAttr('data-avatar');
    });





    var user = $('.user__img');
    user.on('click', function (e) {
        var $user = $(this);
        var userOffset = $user.offset();
        var userTop = userOffset.top;
        var userLeft = userOffset.left;
        var isToped = true;
        var isLefted = true;
        var isRighted = true;
        var isBottom = true;
        var parent = $(this).parent();



        user.each(function (e) {

            var offset = $(this).offset();

            if (isToped) {
                isToped = userTop <= offset.top;
            }
            if (isLefted) {
                isLefted = userLeft <= offset.left;
            }
            if (isBottom) {
                isBottom = userTop + 5 > offset.top;
            }
            if (isRighted) {
                isRighted = userLeft + 5 > offset.left;
            }


        });


        if (!$(e.target).parent().parent().hasClass('user_show')) {
            if (isToped && isLefted) {
                parent.css({ 'transform-origin': 'top left' });
            }
            if (isToped && !isLefted && !isRighted) {
                parent.css({ 'transform-origin': 'top' });
            }
            if (isToped && isRighted) {
                parent.css({ 'transform-origin': 'top right' });
            }
            if (isBottom && isLefted) {
                parent.css({ 'transform-origin': 'bottom left' });
            }
            if (isBottom && !isLefted && !isRighted) {
                parent.css({ 'transform-origin': 'bottom' });
            }
            if (isBottom && isRighted) {
                parent.css({ 'transform-origin': 'bottom right' });
            }
        }


        if (isLefted) {
            parent.parent().toggleClass('user_show-left');
        }

        $('.section_top').toggleClass('section_disable');
        $(e.target).parent().parent().toggleClass('user_show');

        console.log("isToped:", isToped);
        console.log("isLefted:", isLefted);
        console.log("isBottom:", isBottom);
        console.log("isRighted:", isRighted);

    });

    lottie.loadAnimation({
        container: document.getElementById('animation'),
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: 'js/json/main.json',
        rendererSettings: {
            progressiveLoad: true, // Boolean, only svg renderer, loads dom elements when needed. Might speed up initialization for large number of elements.
            hideOnTransparent: true, //Boolean, only svg renderer, hides elements when opacity reaches 0
        }
    });



    lottie.loadAnimation({
        container: document.getElementById('people'),
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: 'js/json/allpeople.json',
        rendererSettings: {
            progressiveLoad: true, // Boolean, only svg renderer, loads dom elements when needed. Might speed up initialization for large number of elements.
            hideOnTransparent: true, //Boolean, only svg renderer, hides elements when opacity reaches 0
        }
    });

    $(window).on('resize', function () {

        var windowWidth = $(this).width();

        if (windowWidth > 992) {
            $('.hot-auction').slick('unslick');
        } else {
            $('.hot-auction').slick({
                infinite: false,
                arrows: true,
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 540,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
        }
    }).trigger('resize');
});

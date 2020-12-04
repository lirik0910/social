
  var tl = new TimelineMax();
  var controller = new ScrollMagic.Controller();

  var elementsArr = document.querySelectorAll('.hot-auction__subscriber');

  var countsArr = [];

  for (var i = 0; i < elementsArr.length; i++) {
    var el = elementsArr[i];
    countsArr.push(el.getAttribute('data-count'));
  }
  var line = document.querySelectorAll('.user__line');


  for (var i = 0; i < line.length; i++) {
    var maxValue = Math.max.apply(Math, countsArr);
    var sum = (countsArr[i] / 100) * maxValue;

    tl.fromTo(line[i], .3, { height: 0 }, { height: sum / 4 });
  }

  var scene = new ScrollMagic.Scene({
    triggerElement: ".hot-auction",
    reverse: false
  })
    .setTween(tl)
    .addTo(controller);
// Navbar behaviour: mobile toggle + dropdown menus. No dependencies.
(function () {
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.getElementById('site-nav');

  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  var menus = document.querySelectorAll('.has-menu');

  function closeAll(except) {
    menus.forEach(function (m) {
      if (m !== except) {
        m.classList.remove('open');
        m.querySelector('.menu-btn').setAttribute('aria-expanded', 'false');
      }
    });
  }

  menus.forEach(function (m) {
    var btn = m.querySelector('.menu-btn');
    btn.addEventListener('click', function (ev) {
      ev.stopPropagation();
      closeAll(m);
      var open = m.classList.toggle('open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });

  document.addEventListener('click', function () { closeAll(null); });
  document.addEventListener('keydown', function (ev) {
    if (ev.key === 'Escape') { closeAll(null); }
  });
})();

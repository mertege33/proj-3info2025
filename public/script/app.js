// app.js — controla ocultação do topbar/website-link e foco nas views
(function () {
    // quais páginas devem esconder o topo e o link
    const hideUIFor = ['cadastro.html', 'menu.html'];
  
    function updateUI() {
      const path = location.pathname.split('/').pop().toLowerCase() || 'homescreen.html';
      const shouldHide = hideUIFor.includes(path);
  
      if (shouldHide) document.body.classList.add('hide-ui');
      else document.body.classList.remove('hide-ui');
  
      // acessibilidade: foco no primeiro elemento relevante
      if (path === 'cadastro.html') {
        const first = document.querySelector('.register-wrapper input, .register-wrapper textarea, .btn.primary');
        if (first) first.focus();
      } else if (path === 'menu.html') {
        const firstMenu = document.querySelector('.menu-item, .module-card');
        if (firstMenu) firstMenu.focus();
      } else {
        const enter = document.getElementById('enterBtn');
        if (enter) enter.focus();
      }
    }
  
    // executar ao carregar e quando o histórico for navegado
    document.addEventListener('DOMContentLoaded', updateUI);
    window.addEventListener('popstate', updateUI);
  
    // optional: parallax sutil para a decor do cadastro
    (function(){
      const decor = document.querySelector('body#page-cadastro .bg-decor');
      if(!decor) return;
      let raf = null;
      document.addEventListener('mousemove', e => {
        if (raf) cancelAnimationFrame(raf);
        raf = requestAnimationFrame(() => {
          const rx = (e.clientX / window.innerWidth - 0.5) * 18; // -9..9
          const ry = (e.clientY / window.innerHeight - 0.5) * 18; // -9..9
          decor.style.transform = `translate3d(${rx * -1}px, ${ry * -1}px, 0) rotate(${rx * 0.2}deg)`;
        });
      }, {passive:true});
    })();
  
  })();
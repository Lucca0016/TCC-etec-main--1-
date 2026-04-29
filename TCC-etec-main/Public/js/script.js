// script.js — comportamento básico da página

document.addEventListener('DOMContentLoaded', function(){
  // Mobile nav toggle
  const nav = document.getElementById('main-nav');
  const toggle = document.querySelector('.nav-toggle');
  toggle.addEventListener('click', function(){
    const open = nav.classList.toggle('open');
    toggle.setAttribute('aria-expanded', String(open));
  });

  // Smooth scrolling for internal links
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', function(e){
      const href = this.getAttribute('href');
      const targetId = href.substring(1);
      const target = document.getElementById(targetId);
      // prevent default for hash links to avoid sudden jumps; if target exists, smooth scroll
      e.preventDefault();
      if(target){
        const yOffset = 68; // account for sticky header
        const y = target.getBoundingClientRect().top + window.pageYOffset - yOffset;
        window.scrollTo({top: y, behavior: 'smooth'});
      } else if(href === '#' || href === ''){
        // do nothing for empty hashes
        return;
      }
      // close mobile nav if open
      if(nav.classList.contains('open')){
        nav.classList.remove('open');
        toggle.setAttribute('aria-expanded','false');
      }
    })
  });

  // Active menu highlighting on scroll
  const sections = Array.from(document.querySelectorAll('main section[id]'));
  const navLinks = Array.from(document.querySelectorAll('.nav-list a'));

  function onScroll(){
    const scrollPos = window.scrollY + 80;
    if(sections.length === 0) return;
    let current = sections[0];
    for(const sec of sections){
      if(sec.offsetTop <= scrollPos) current = sec;
    }
    navLinks.forEach(a => a.classList.toggle('active-link', a.getAttribute('href') === `#${current.id}`));
  }

  window.addEventListener('scroll', onScroll);
  onScroll();

  // set current year in footer
  const yearEl = document.getElementById('year');
  if(yearEl) yearEl.textContent = new Date().getFullYear();
});

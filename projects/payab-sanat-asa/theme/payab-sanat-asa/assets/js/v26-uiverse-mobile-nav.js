/* V26 mobile navigation */
(()=>{
  const nav=document.getElementById('payab-mobile-nav');
  if(!nav)return;
  const links=[...nav.querySelectorAll('[data-mini-nav]')];
  const search=nav.querySelector('.payab-mobile-search');
  const searchButton=nav.querySelector('[data-mini-nav="search"]');
  const input=search?.querySelector('input');
  const close=nav.querySelector('.payab-mobile-search-close');
  const setActive=(key)=>{
    links.forEach(link=>{
      const yes=link.dataset.miniNav===key;
      link.classList.toggle('is-active',yes);
      if(yes&&key!=='search'){link.setAttribute('aria-current','location')}
      else{link.removeAttribute('aria-current')}
    });
  };
  const closeSearch=()=>{if(!search)return;search.hidden=true;searchButton?.setAttribute('aria-expanded','false')};
  links.forEach(link=>{
    if(link===searchButton)return;
    link.addEventListener('click',()=>{closeSearch();setActive(link.dataset.miniNav)});
  });
  searchButton?.addEventListener('click',()=>{
    const open=search?.hidden;
    if(!search)return;
    if(open){search.hidden=false;searchButton.setAttribute('aria-expanded','true');setActive('search');requestAnimationFrame(()=>input?.focus());}
    else{closeSearch();setActive('home');}
  });
  close?.addEventListener('click',()=>{closeSearch();setActive('home');searchButton?.focus()});
  document.addEventListener('pointerdown',e=>{if(search&&!search.hidden&&!nav.contains(e.target)){closeSearch()}});
  document.addEventListener('keydown',e=>{if(e.key==='Escape'&&search&&!search.hidden){closeSearch();setActive('home');searchButton?.focus()}});
  // Sync expanded item when relevant page sections enter the viewport.
  if('IntersectionObserver' in window){
    const sections=[['home',document.querySelector('#content')],['categories',document.querySelector('#categories')],['products',document.querySelector('#products')]].filter(([,el])=>!!el);
    const observer=new IntersectionObserver(entries=>{
      if(search&&!search.hidden)return;
      const visible=entries.filter(e=>e.isIntersecting).sort((a,b)=>b.intersectionRatio-a.intersectionRatio)[0];
      if(visible){const matching=sections.find(([,el])=>el===visible.target);if(matching)setActive(matching[0]);}
    },{rootMargin:'-28% 0px -48% 0px',threshold:[0,.1,.3,.5]});
    sections.forEach(([,el])=>observer.observe(el));
  }
})();

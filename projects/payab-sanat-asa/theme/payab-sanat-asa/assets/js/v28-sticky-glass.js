/* V28 — toggles a purely visual scroll class, no menu or layout mutation */
(()=>{
  const header=document.querySelector('.payab-header.header');
  if(!header)return;
  let scheduled=false;
  const sync=()=>{header.classList.toggle('is-glass-sticky',window.scrollY>24);scheduled=false;};
  window.addEventListener('scroll',()=>{if(!scheduled){scheduled=true;requestAnimationFrame(sync);}}, {passive:true});
  window.addEventListener('pageshow',sync);
  sync();
})();

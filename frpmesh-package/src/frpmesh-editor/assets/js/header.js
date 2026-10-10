window.FrpMeshEditor=window.FrpMeshEditor||{programs:{}};
window.FrpMeshEditor.programs["header"]=function(ctx){
const document=ctx.document,window=ctx.window,addEventListener=ctx.addEventListener,requestAnimationFrame=ctx.requestAnimationFrame,cancelAnimationFrame=ctx.cancelAnimationFrame,setTimeout=ctx.setTimeout,clearTimeout=ctx.clearTimeout,IntersectionObserver=ctx.IntersectionObserver,ResizeObserver=ctx.ResizeObserver,matchMedia=ctx.matchMedia;
ctx.run("header-0",["mobile-menu","menu-toggle","close-menu"],[".site-header"],()=>{
(()=>{
const menu=document.getElementById('mobile-menu'),toggle=document.getElementById('menu-toggle');
function closeMenu(){menu.close();}
toggle.addEventListener('click',()=>{menu.showModal();toggle.setAttribute('aria-expanded','true');document.body.style.overflow='hidden';});
document.getElementById('close-menu').addEventListener('click',closeMenu);
menu.addEventListener('close',()=>{toggle.setAttribute('aria-expanded','false');document.body.style.overflow='';toggle.focus();});
menu.addEventListener('click',e=>{if(e.target!==menu)return;const r=menu.getBoundingClientRect();if(e.clientX<r.left||e.clientX>r.right||e.clientY<r.top||e.clientY>r.bottom)closeMenu();});
menu.querySelectorAll('a').forEach(a=>a.addEventListener('click',closeMenu));

function refreshHeader(){const header=document.querySelector('.site-header');if(header)header.classList.toggle('is-scrolled',window.scrollY>24);}
window.addEventListener('scroll',refreshHeader,{passive:true});refreshHeader();
})();
});
};if(window.FrpMeshEditor.schedule)window.FrpMeshEditor.schedule();

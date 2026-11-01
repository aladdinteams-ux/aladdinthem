/* V27 settings sync + robust drawer accessibility */
(()=>{
const drawer=document.getElementById('mobile-panel');
if(!drawer)return;
const mobileTheme=document.getElementById('payab-drawer-theme');
const desktopTheme=document.getElementById('theme-toggle');
const html=document.documentElement;
function sync(){
 const on=html.dataset.theme==='dark';
 mobileTheme?.setAttribute('aria-pressed',String(on));
 if(desktopTheme){desktopTheme.setAttribute('aria-pressed',String(on));}
}
mobileTheme?.addEventListener('click',()=>{
 if(desktopTheme){desktopTheme.click();}
 else{html.dataset.theme=html.dataset.theme==='dark'?'light':'dark';try{localStorage.setItem('payab-theme',html.dataset.theme)}catch(e){}}
 sync();
});
desktopTheme?.addEventListener('click',sync);
// Close the drawer upon leaving mobile breakpoint, repairing scroll lock.
const mq=window.matchMedia('(min-width:961px)');
function onBreakpoint(e){if(!e.matches)return;drawer.classList.remove('open');document.getElementById('drawer-overlay')?.classList.remove('visible');drawer.setAttribute('aria-hidden','true');document.getElementById('menu-open')?.setAttribute('aria-expanded','false');document.body.style.overflow='';}
if(mq.addEventListener)mq.addEventListener('change',onBreakpoint);else mq.addListener(onBreakpoint);
sync();
})();

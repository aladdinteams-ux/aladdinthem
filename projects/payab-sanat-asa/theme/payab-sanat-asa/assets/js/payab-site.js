/* Aladdin Payab Sanat Asa - merged from the approved HTML inline scripts. Language data comes from window.PayabAsa. */
(()=>{
const open=document.getElementById('menu-open'),close=document.getElementById('menu-close'),panel=document.getElementById('mobile-panel'),overlay=document.getElementById('drawer-overlay');let previous=null;
function setMenu(isOpen){if(!open||!close||!panel||!overlay)return;if(isOpen)previous=document.activeElement;panel.classList.toggle('open',isOpen);overlay.classList.toggle('visible',isOpen);open.setAttribute('aria-expanded',String(isOpen));panel.setAttribute('aria-hidden',String(!isOpen));document.body.style.overflow=isOpen?'hidden':'';if(isOpen)close.focus();else if(previous)previous.focus();}
if(open&&close&&panel&&overlay){open.addEventListener('click',()=>setMenu(true));close.addEventListener('click',()=>setMenu(false));overlay.addEventListener('click',()=>setMenu(false));panel.querySelectorAll('nav a').forEach(a=>a.addEventListener('click',()=>setMenu(false)));document.addEventListener('keydown',e=>{if(e.key==='Escape'&&panel.classList.contains('open'))setMenu(false);if(e.key==='Tab'&&panel.classList.contains('open')){const nodes=[...panel.querySelectorAll('button,a')].filter(el=>!el.disabled);if(!nodes.length)return;if(e.shiftKey&&document.activeElement===nodes[0]){e.preventDefault();nodes[nodes.length-1].focus();}else if(!e.shiftKey&&document.activeElement===nodes[nodes.length-1]){e.preventDefault();nodes[0].focus();}}});}
const filters=[...document.querySelectorAll('[data-filter]')],products=[...document.querySelectorAll('[data-type]')];function filterProducts(type){filters.forEach(b=>b.setAttribute('aria-pressed',String(b.dataset.filter===type)));products.forEach(card=>{card.hidden=type!=='all'&&card.dataset.type!==type;});}filters.forEach(b=>b.addEventListener('click',()=>filterProducts(b.dataset.filter)));document.querySelectorAll('[data-category-jump]').forEach(a=>a.addEventListener('click',()=>filterProducts(a.dataset.categoryJump)));
const toggle=document.getElementById('theme-toggle');const html=document.documentElement;const strings={fa:['فعال کردن حالت شب','فعال کردن حالت روز'],en:['Switch to dark mode','Switch to light mode'],ar:['تفعيل الوضع الليلي','تفعيل الوضع النهاري']};const language=html.lang in strings?html.lang:'fa';
function sync(){const dark=html.dataset.theme==='dark';toggle.setAttribute('aria-pressed',String(dark));toggle.setAttribute('aria-label',strings[language][dark?1:0]);document.querySelector('meta[name="theme-color"]')?.setAttribute('content',dark?'#0b1b2b':'#0E2E4C');}
toggle?.addEventListener('click',()=>{const next=html.dataset.theme==='dark'?'light':'dark';html.dataset.theme=next;try{localStorage.setItem('payab-theme',next)}catch(e){}sync();});if(toggle)sync();
})();
document.addEventListener('DOMContentLoaded',()=>{const w=document.querySelector('.payab-header .header-search-wrap'),b=w?.querySelector('.search-expand-trigger'),i=w?.querySelector('input');b?.addEventListener('click',()=>{w.classList.add('is-open');i?.focus()});document.addEventListener('pointerdown',(e)=>{if(w&&!w.contains(e.target))w.classList.remove('is-open')});document.addEventListener('keydown',(e)=>{if(e.key==='Escape')w?.classList.remove('is-open')})});
(function(){ const buttons=document.querySelectorAll('.asa-fav'); if(!buttons.length)return; buttons.forEach(b=>{ const key='payab-favorite-'+b.dataset.favorite; try{b.setAttribute('aria-pressed',localStorage.getItem(key)==='1'?'true':'false')}catch(e){}; b.addEventListener('click',()=>{const isOn=b.getAttribute('aria-pressed')==='true';b.setAttribute('aria-pressed',isOn?'false':'true');b.setAttribute('aria-label',isOn?((window.PayabAsa&&PayabAsa.favAdd)||''):((window.PayabAsa&&PayabAsa.favRemove)||''));try{localStorage.setItem(key,isOn?'0':'1')}catch(e){} })})})();
(()=>{
const h=document.querySelector('.payab-header');
const panel=document.getElementById('payab-products-mega');
const nav=h?.querySelector('[data-mega-parent="products"]');
const trigger=nav?.querySelector('.mega-menu-trigger');
if(!h||!panel||!nav||!trigger)return;
const tabs=[...panel.querySelectorAll('.m9-category-tab')];
const box=panel.querySelector('.m9-feature-copy');
const heading=box?.querySelector('h3');
const subheading=box?.querySelector('.m9-eyebrow');
const desc=box?.querySelector('p');
const explore=box?.querySelector('.m9-more');
const img=panel.querySelector('.m9-feature-row .m9-img img');
const data=(window.PayabAsa&&PayabAsa.mega)||{};
const assetBase=((window.PayabAsa&&PayabAsa.assets)||'assets')+'/';
let open=false;
let closeTimer=0;
const finePointer=()=>window.matchMedia('(hover:hover) and (pointer:fine)').matches;
function cancelClose(){if(closeTimer){window.clearTimeout(closeTimer);closeTimer=0;}}
function setOpen(yes){cancelClose();if(open===yes)return;open=yes;h.classList.toggle('mega-is-open',yes);trigger.setAttribute('aria-expanded',String(yes));panel.setAttribute('aria-hidden',String(!yes));}
function scheduleClose(){cancelClose();closeTimer=window.setTimeout(()=>{if(!nav.matches(':hover')&&!panel.matches(':hover'))setOpen(false);},230);}
function selectCategory(key){const row=data[key];if(!row)return;
  tabs.forEach(t=>t.setAttribute('aria-pressed',String(t.dataset.megaCategory===key)));
  if(heading) heading.textContent=row[0];
  if(desc) desc.textContent=row[1];
  if(subheading) subheading.textContent=row[4];
  if(explore){const arrow=explore.querySelector('[aria-hidden]');explore.textContent=row[2]+' ';if(arrow)explore.appendChild(arrow);else{const a=document.createElement('span');a.setAttribute('aria-hidden','true');a.textContent=' ↗';explore.appendChild(a)}
  explore.href='#products';explore.dataset.categoryJump=key;}
  if(img){img.src=assetBase+row[5];img.alt=row[3];}
}
tabs.forEach(tab=>{tab.addEventListener('click',()=>selectCategory(tab.dataset.megaCategory));});
selectCategory('electric');
if(finePointer()){
 nav.addEventListener('pointerenter',()=>{cancelClose();setOpen(true);});
 nav.addEventListener('pointerleave',scheduleClose);
 panel.addEventListener('pointerenter',cancelClose);
 panel.addEventListener('pointerleave',scheduleClose);
}
trigger.addEventListener('click',(e)=>{e.preventDefault();setOpen(!open);});
trigger.addEventListener('keydown',e=>{if(e.key==='ArrowDown'){e.preventDefault();setOpen(true);tabs[0]?.focus();}});
nav.querySelector('a')?.addEventListener('focus',()=>{setOpen(true)});
panel.addEventListener('focusin',cancelClose);
document.addEventListener('pointerdown',e=>{if(open&&!h.contains(e.target))setOpen(false)});
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&open){setOpen(false);trigger.focus();}});
document.addEventListener('focusin',e=>{if(open&&!nav.contains(e.target)&&!panel.contains(e.target))setOpen(false)});
panel.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>setOpen(false)));
})();
(()=>{
const mobileLinks=[...document.querySelectorAll('.mobile-bottom-nav [data-mobile-nav]')];
if(!mobileLinks.length)return;
const map={home:['#content','.hero'],categories:['#categories'],products:['#products'],contact:['footer','#site-footer']};
const setActive=(name)=>mobileLinks.forEach(a=>a.classList.toggle('active',a.dataset.mobileNav===name));
mobileLinks.forEach(a=>a.addEventListener('click',()=>setActive(a.dataset.mobileNav)));
if('IntersectionObserver' in window){
  const sections=[];
  Object.entries(map).forEach(([name,selectors])=>{selectors.some(sel=>{const el=document.querySelector(sel);if(el){sections.push([name,el]);return true;}return false;});});
  const obs=new IntersectionObserver(entries=>{
    const visible=entries.filter(e=>e.isIntersecting).sort((a,b)=>b.intersectionRatio-a.intersectionRatio)[0];
    if(!visible)return;
    const found=sections.find(([,el])=>el===visible.target);
    if(found)setActive(found[0]);
  },{rootMargin:'-35% 0px -45% 0px',threshold:[0.12,0.3,0.55]});
  sections.forEach(([,el])=>obs.observe(el));
}
})();

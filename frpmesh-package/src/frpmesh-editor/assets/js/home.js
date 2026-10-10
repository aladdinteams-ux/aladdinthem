window.FrpMeshEditor=window.FrpMeshEditor||{programs:{}};
window.FrpMeshEditor.programs["home"]=function(ctx){
const FormData=ctx.FormData,document=ctx.document,window=ctx.window,addEventListener=ctx.addEventListener,requestAnimationFrame=ctx.requestAnimationFrame,cancelAnimationFrame=ctx.cancelAnimationFrame,setTimeout=ctx.setTimeout,clearTimeout=ctx.clearTimeout,IntersectionObserver=ctx.IntersectionObserver,ResizeObserver=ctx.ResizeObserver,matchMedia=ctx.matchMedia;
const questions=[...document.querySelectorAll('.faq-list details')],reducedMotion=window.matchMedia('(prefers-reduced-motion: reduce)'),numberFormat=new Intl.NumberFormat('fa-IR',{maximumFractionDigits:2});
function updateScroll(){const el=document.getElementById('read-progress');if(el){const max=document.documentElement.scrollHeight-innerHeight;el.style.transform='scaleX('+(max>0?Math.min(1,Math.max(0,scrollY/max)):0)+')';}}
ctx.run("home-native-0",[],[".faq-list details"],()=>{
questions.forEach(q=>q.addEventListener('toggle',()=>{if(q.open)questions.forEach(other=>{if(other!==q)other.open=false;});}));


});
ctx.run("home-native-1",["read-progress", "back-top"],[],()=>{
// Scroll progress and subtle section reveals; all text remains usable without JS.
const reducedMotion=window.matchMedia('(prefers-reduced-motion: reduce)');
const progress=document.getElementById('read-progress'),backTop=document.getElementById('back-top');
let scrollQueued=false;
function updateScroll(){const max=document.documentElement.scrollHeight-innerHeight;progress.style.transform='scaleX('+(max>0?Math.min(1,Math.max(0,scrollY/max)):0)+')';backTop.classList.toggle('visible',scrollY>700);scrollQueued=false;}
addEventListener('scroll',()=>{if(!scrollQueued){scrollQueued=true;requestAnimationFrame(updateScroll);}},{passive:true});
addEventListener('resize',updateScroll);updateScroll();
backTop.addEventListener('click',()=>window.scrollTo({top:0,behavior:reducedMotion.matches?'auto':'smooth'}));
if('IntersectionObserver' in window){
 const navLinks=[...document.querySelectorAll('.section-nav-links a')];
 const navObserver=new IntersectionObserver(entries=>{const visible=entries.filter(e=>e.isIntersecting).sort((a,b)=>b.intersectionRatio-a.intersectionRatio);if(visible.length){navLinks.forEach(a=>{const active=a.getAttribute('href')==='#'+visible[0].target.id;a.classList.toggle('is-current',active);if(active)a.setAttribute('aria-current','location');else a.removeAttribute('aria-current');});}},{rootMargin:'-15% 0px -45% 0px',threshold:[0,.1,.4]});
 navLinks.forEach(a=>{const el=document.querySelector(a.getAttribute('href'));if(el)navObserver.observe(el);});
 if(!reducedMotion.matches){document.documentElement.classList.add('reveal-enabled');const revealObserver=new IntersectionObserver(entries=>entries.forEach(e=>{if(e.isIntersecting){e.target.classList.remove('pending');e.target.classList.add('shown');revealObserver.unobserve(e.target);}}),{threshold:.08});
 document.querySelectorAll('.section-head,.service-card,.intro-layout,.benefits>div,.material-card,.process-copy,.steps li,.price-content,.tools-layout,.project,.contact-card').forEach(el=>{el.classList.add('reveal');if(el.getBoundingClientRect().top>innerHeight){el.classList.add('pending');revealObserver.observe(el);}});
 reducedMotion.addEventListener('change',e=>{if(e.matches){document.querySelectorAll('.reveal.pending').forEach(el=>el.classList.remove('pending'));revealObserver.disconnect();}});
 }
}

});
ctx.run("home-native-2",["recommendation-title", "recommendation-text", "recommendation-link"],[],()=>{
// Useful service selection rather than artificial activity.
const recommendations={pool:[ctx.text("home","js_1","بررسی آب‌بندی کف و دیواره‌ها"),ctx.text("home","js_2","ابعاد، محل نشتی، وضعیت پوشش قبلی و جزئیات نازل‌ها و لوله‌ها را آماده کنید. برای جکوزی، دمای بهره‌برداری نیز اهمیت دارد.")],tank:[ctx.text("home","js_3","بررسی سازگاری پوشش مخزن"),ctx.text("home","js_4","جنس مخزن، نوع ماده، غلظت و دمای بهره‌برداری را مشخص کنید. انتخاب رزین باید با داده‌های سازگاری شیمیایی و شرایط تماس هماهنگ شود.")],negative:[ctx.text("home","js_5","بررسی نفوذ آب و فشار منفی"),ctx.text("home","js_6","تصاویر کف و دیواره و توضیح محل و زمان ورود آب را آماده کنید. وضعیت سازه و مسیر نفوذ آب باید پیش از انتخاب روش بررسی شوند.")],building:[ctx.text("home","js_7","بررسی بستر و جزئیات ساختمانی"),ctx.text("home","js_8","نوع سطح، شیب و زهکشی، پوشش فعلی و نقاط اتصال را توضیح دهید. تصاویر نواحی نم‌زده و ترک‌ها به بررسی اولیه کمک می‌کنند.")]};
document.querySelectorAll('[data-project]').forEach(button=>button.addEventListener('click',()=>{document.querySelectorAll('[data-project]').forEach(b=>b.setAttribute('aria-pressed',String(b===button)));const key=button.dataset.project;document.getElementById('recommendation-title').textContent=recommendations[key][0];document.getElementById('recommendation-text').textContent=recommendations[key][1];document.getElementById('recommendation-link').href='#'+key;}));

});
ctx.run("home-native-3",["estimate-form", "estimate-error", "estimate-total", "estimate-floor", "estimate-walls", "estimate-result", "reset-estimate", "interaction-toast", "copy-estimate"],[],()=>{
// Rectangular pool area estimate — no invented price or material quantity.
const estimateForm=document.getElementById('estimate-form');
function dimensions(){const f=new FormData(estimateForm);return ['length','width','depth'].map(key=>Number(f.get(key)));}
function calculateArea(focusResult=false){const [l,w,d]=dimensions();const error=document.getElementById('estimate-error');if(!estimateForm.checkValidity()||![l,w,d].every(n=>Number.isFinite(n)&&n>0)){error.textContent=ctx.text("home","js_9","ابعاد را به‌صورت عدد مثبت و در محدوده مشخص‌شده وارد کنید.");return false;}error.textContent='';const floor=l*w,walls=2*(l+w)*d;document.getElementById('estimate-total').textContent=numberFormat.format(floor+walls);document.getElementById('estimate-floor').value=numberFormat.format(floor);document.getElementById('estimate-walls').value=numberFormat.format(walls);if(focusResult)document.getElementById('estimate-result').focus({preventScroll:true});return true;}
estimateForm.addEventListener('submit',e=>{e.preventDefault();calculateArea(true);});
estimateForm.querySelectorAll('input').forEach(input=>input.addEventListener('input',()=>{document.getElementById('estimate-total').textContent='—';document.getElementById('estimate-floor').value='—';document.getElementById('estimate-walls').value='—';document.getElementById('estimate-error').textContent=ctx.text("home","js_10","برای به‌روزرسانی نتیجه، محاسبه متراژ را بزنید.");}));
document.getElementById('reset-estimate').addEventListener('click',()=>{estimateForm.reset();calculateArea();});
let messageTimer;function toast(message){const el=document.getElementById('interaction-toast');el.textContent=message;clearTimeout(messageTimer);messageTimer=setTimeout(()=>el.textContent='',4000);}
async function copySummary(text){try{if(navigator.clipboard&&isSecureContext)await navigator.clipboard.writeText(text);else{const field=document.createElement('textarea');field.value=text;field.style.position='fixed';field.style.opacity='0';document.body.append(field);field.select();const copied=document.execCommand('copy');field.remove();if(!copied)throw Error('copy');}toast(ctx.text("home","js_11","خلاصه ابعاد و متراژ کپی شد."));}catch{toast(ctx.text("home","js_12","کپی خودکار ممکن نشد. خلاصه ابعاد را از نتیجه محاسبه بردارید."));}}
document.getElementById('copy-estimate').addEventListener('click',()=>{if(!calculateArea()){estimateForm.reportValidity();return;}const [l,w,d]=dimensions();copySummary(ctx.text("home","js_13","استخر مستطیلی با عمق ثابت\nطول: ")+l+ctx.text("home","js_14"," متر\nعرض: ")+w+ctx.text("home","js_15"," متر\nعمق: ")+d+ctx.text("home","js_16"," متر\nمساحت کف و دیواره‌ها: ")+numberFormat.format(l*w+2*(l+w)*d)+ctx.text("home","js_17"," مترمربع\nبرآورد اولیه، بدون پله، شیب، بازشو و پرت مصالح."));});

});
ctx.run("home-native-4",["faq-query", "faq-empty", "faq-result-count", "reset-faq"],[],()=>{
// Search visible FAQ text, preserving the original content in the document.
function normalizeFa(text){return text.replace(/ي/g,'ی').replace(/ك/g,'ک').replace(/[\u200c\u200f\u200e]/g,' ').replace(/\s+/g,' ').trim().toLowerCase();}
const faqQuery=document.getElementById('faq-query');function filterFaq(){const q=normalizeFa(faqQuery.value),terms=q.split(' ').filter(Boolean);let matches=0;questions.forEach(item=>{const text=normalizeFa(item.textContent);const match=terms.every(term=>text.includes(term));item.hidden=!match;if(match)matches++;});document.getElementById('faq-empty').hidden=matches>0;document.getElementById('faq-result-count').textContent=q?numberFormat.format(matches)+ctx.text("home","js_18"," پاسخ مرتبط"):'';updateScroll();}
faqQuery.addEventListener('input',filterFaq);document.getElementById('reset-faq').addEventListener('click',()=>{faqQuery.value='';filterFaq();faqQuery.focus();});

});
ctx.run("home-native-5",["gallery-dialog", "gallery-image", "gallery-caption", "gallery-counter", "close-gallery", "gallery-prev", "gallery-next"],[".project-trigger"],()=>{
// Native dialog handles focus trapping and Escape; arrow keys follow RTL gallery order.
const gallery=document.getElementById('gallery-dialog'),projectButtons=[...document.querySelectorAll('.project-trigger')];let galleryIndex=0,galleryOpener=null;
function renderGallery(index){galleryIndex=(index+projectButtons.length)%projectButtons.length;const button=projectButtons[galleryIndex],img=button.querySelector('img');const target=document.getElementById('gallery-image');target.src=img.src;target.alt=img.alt;document.getElementById('gallery-caption').textContent=button.closest('figure').querySelector('figcaption').textContent.trim();document.getElementById('gallery-counter').textContent=numberFormat.format(galleryIndex+1)+' / '+numberFormat.format(projectButtons.length);}
projectButtons.forEach((button,index)=>button.addEventListener('click',()=>{galleryOpener=button;renderGallery(index);gallery.showModal();document.body.style.overflow='hidden';}));
document.getElementById('close-gallery').addEventListener('click',()=>gallery.close());document.getElementById('gallery-prev').addEventListener('click',()=>renderGallery(galleryIndex-1));document.getElementById('gallery-next').addEventListener('click',()=>renderGallery(galleryIndex+1));
gallery.addEventListener('keydown',e=>{if(e.key==='ArrowLeft'){e.preventDefault();renderGallery(galleryIndex+1);}else if(e.key==='ArrowRight'){e.preventDefault();renderGallery(galleryIndex-1);}});
gallery.addEventListener('close',()=>{document.body.style.overflow=document.getElementById('mobile-menu')?.open?'hidden':'';if(galleryOpener)galleryOpener.focus({preventScroll:true});});gallery.addEventListener('click',e=>{if(e.target!==gallery)return;const r=gallery.getBoundingClientRect();if(e.clientX<r.left||e.clientX>r.right||e.clientY<r.top||e.clientY>r.bottom)gallery.close();});


});
ctx.run("home-native-6",["layer-stack", "layer-title", "layer-text", "layer-active-label"],[".layer-description"],()=>{
// Conceptual exploded layer illustration; not an execution specification.
const layerContent={substrate:[ctx.text("home","js_19","از بستر شروع می‌شود."),ctx.text("home","js_20","وضعیت زیرکار، رطوبت و جزئیات اتصال پیش از اجرای پوشش بررسی می‌شوند. روش آماده‌سازی به بستر و سیستم انتخاب‌شده بستگی دارد.")],fibers:[ctx.text("home","js_21","دو جزء، یک ساختار کامپوزیتی."),ctx.text("home","js_22","الیاف شیشه نقش تقویت‌کننده و رزین نقش زمینه را دارد. نوع و آرایش مواد باید با شرایط کاری و طراحی سیستم هماهنگ باشند.")],finish:[ctx.text("home","js_23","سطح نهایی، متناسب با کاربرد."),ctx.text("home","js_24","انتخاب پوشش نهایی و شرایط عمل‌آوری به سیستم منتخب وابسته است. این تصویر، نمایش مفهومی است و جایگزین مشخصات فنی اجرای پروژه نیست.")]};
const layerButtons=[...document.querySelectorAll('[data-layer-choice]')];
const layerDescription=document.querySelector('.layer-description');
let layerTextAnimation=null;
function selectLayer(button){const key=button.dataset.layerChoice;if(!layerContent[key])return;const stack=document.getElementById('layer-stack');if(stack.dataset.layer===key)return;layerButtons.forEach(item=>item.setAttribute('aria-pressed',String(item===button)));stack.dataset.layer=key;document.getElementById('layer-title').textContent=layerContent[key][0];document.getElementById('layer-text').textContent=layerContent[key][1];document.getElementById('layer-active-label').textContent=button.textContent.trim();if(layerTextAnimation){layerTextAnimation.cancel();layerTextAnimation=null;}if(!reducedMotion.matches&&typeof layerDescription.animate==='function'){layerTextAnimation=layerDescription.animate([{opacity:.25,transform:'translateY(8px)'},{opacity:1,transform:'translateY(0)'}],{duration:380,easing:'cubic-bezier(.22,1,.36,1)'});}}
layerButtons.forEach(button=>button.addEventListener('click',()=>selectLayer(button)));



});
ctx.run("home-native-7",[],[".hero"],()=>{
// Pause the decorative grid when the hero is outside the viewport.
const animatedHero=document.querySelector('.hero');
if(animatedHero&&'IntersectionObserver' in window){const gridObserver=new IntersectionObserver(entries=>entries.forEach(entry=>animatedHero.classList.toggle('grid-paused',!entry.isIntersecting)),{threshold:0});gridObserver.observe(animatedHero);}


});
ctx.run("home-native-8",["proposal-status"],[".proposal-check"],()=>{
// A voluntary proposal review checklist; no claims about company certifications or guarantees.
const proposalChecks=[...document.querySelectorAll('.proposal-check')];
function updateProposalChecklist(){const count=proposalChecks.filter(input=>input.checked).length;document.getElementById('proposal-status').textContent=new Intl.NumberFormat('fa-IR').format(count)+ctx.text("home","js_25"," از ۴ مورد بررسی شده");}
proposalChecks.forEach(input=>input.addEventListener('change',updateProposalChecklist));

});
ctx.run("home-1",["home-articles-track","home-article-prev","home-article-next","home-article-position"],[],()=>{
(()=>{const track=document.getElementById('home-articles-track'),cards=[...track.querySelectorAll('.home-article-card')],prev=document.getElementById('home-article-prev'),next=document.getElementById('home-article-next'),status=document.getElementById('home-article-position');if(!cards.length){prev.disabled=true;next.disabled=true;status.textContent=ctx.text("home","js_26","۰ مطلب");return;}let active=0,frame=0;const reduced=matchMedia('(prefers-reduced-motion:reduce)');function measure(){const tr=track.getBoundingClientRect();let best=Infinity;cards.forEach((c,i)=>{const d=Math.abs(tr.right-c.getBoundingClientRect().right);if(d<best){best=d;active=i;}});const last=cards[cards.length-1].getBoundingClientRect();prev.disabled=active===0;next.disabled=last.left>=tr.left-3;status.textContent=new Intl.NumberFormat('fa-IR').format(active+1)+ctx.text("home","js_27"," از ")+new Intl.NumberFormat('fa-IR').format(cards.length);frame=0;}function go(i){const index=Math.max(0,Math.min(cards.length-1,i));const delta=cards[index].getBoundingClientRect().right-track.getBoundingClientRect().right+2;track.scrollBy({left:delta,behavior:reduced.matches?'auto':'smooth'});}prev.addEventListener('click',()=>go(active-1));next.addEventListener('click',()=>go(active+1));track.addEventListener('scroll',()=>{if(!frame)frame=requestAnimationFrame(measure);},{passive:true});track.addEventListener('keydown',e=>{if(e.target!==track)return;if(e.key==='ArrowLeft'){e.preventDefault();go(active+1);}if(e.key==='ArrowRight'){e.preventDefault();go(active-1);}if(e.key==='Home'){e.preventDefault();go(0);}if(e.key==='End'){e.preventDefault();go(cards.length-1);}});if('ResizeObserver' in window)new ResizeObserver(measure).observe(track);else addEventListener('resize',measure);measure();})();
});
};if(window.FrpMeshEditor.schedule)window.FrpMeshEditor.schedule();

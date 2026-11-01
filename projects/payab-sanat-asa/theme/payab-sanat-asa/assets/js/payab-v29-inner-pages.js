
(()=>{
const filters=document.querySelector('[data-shop-filters]');const cards=[...document.querySelectorAll('[data-pkind]')];
if(filters){filters.addEventListener('click',e=>{const b=e.target.closest('[data-kind]');if(!b)return;filters.querySelectorAll('[data-kind]').forEach(x=>x.setAttribute('aria-pressed',String(x===b)));cards.forEach(x=>x.hidden=b.dataset.kind!=='all'&&x.dataset.pkind!==b.dataset.kind)});}
const main=document.querySelector('.ip-main-photo img');document.querySelectorAll('.ip-thumb').forEach(b=>b.addEventListener('click',()=>{if(!main)return;document.querySelectorAll('.ip-thumb').forEach(x=>x.classList.toggle('is-selected',x===b));main.src=b.querySelector('img')?.src||main.src;}));
const dialog=document.getElementById('ip-lightbox');document.querySelectorAll('.ip-gallery-item').forEach(b=>b.addEventListener('click',()=>{if(!dialog)return;dialog.querySelector('img').src=b.dataset.image;dialog.querySelector('img').alt=b.dataset.caption;dialog.querySelector('p').textContent=b.dataset.caption;dialog.showModal()}));
dialog?.querySelector('.ip-lightbox-close')?.addEventListener('click',()=>dialog.close());
dialog?.addEventListener('click',e=>{if(e.target===dialog)dialog.close()});
})();

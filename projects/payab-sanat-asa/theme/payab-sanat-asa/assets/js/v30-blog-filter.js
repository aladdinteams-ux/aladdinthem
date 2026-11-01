document.addEventListener('DOMContentLoaded',()=>{
 const root=document.querySelector('.v30-blog-archive');if(!root)return;
 const input=root.querySelector('#blog-search'), clear=root.querySelector('#blog-clear'),
  count=root.querySelector('#blog-count'),empty=root.querySelector('#blog-empty');
 const buttons=[...root.querySelectorAll('[data-blog-filter]')],cards=[...root.querySelectorAll('.v30-blog-card')];
 let category='all';
 const normalized=s=>s.normalize('NFKC').toLocaleLowerCase().replace(/[ي]/g,'ی').replace(/[ك]/g,'ک').trim();
 function update(){
   const q=normalized(input.value);let shown=0;
   cards.forEach(card=>{const visible=(category==='all'||card.dataset.category===category)&&(!q||normalized(card.dataset.search).includes(q));card.hidden=!visible;if(visible)shown++});
   count.textContent=shown+' '+(document.documentElement.lang==='fa'?'مقاله':document.documentElement.lang==='ar'?'مقالات':'articles');
   empty.hidden=shown!==0;clear.hidden=!q;
 }
 buttons.forEach(btn=>btn.addEventListener('click',()=>{category=btn.dataset.blogFilter;buttons.forEach(b=>b.setAttribute('aria-pressed',String(b===btn)));update()}));
 input.addEventListener('input',update);clear.addEventListener('click',()=>{input.value='';input.focus();update()});
 update();
});
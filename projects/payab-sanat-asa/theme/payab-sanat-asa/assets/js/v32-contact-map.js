/* V32 contact form: static HTML preview; no server submission */
(function(){
  const form=document.querySelector('[data-psa-contact-form]');
  if(!form)return;
  const result=form.querySelector('[data-psa-contact-result]');
  const resultText=form.querySelector('[data-psa-contact-result-text]');
  const status=form.querySelector('[data-psa-contact-status]');
  const copyAgain=form.querySelector('[data-psa-contact-copy-again]');
  const fields=['name','phone','email','topic','message'];
  function numberOfDigits(value){return (value.match(/[0-9۰-۹٠-٩]/g)||[]).length;}
  function buildMessage(){
    const t=(key)=>form.getAttribute('data-'+key)||key;
    const value=(key)=>form.elements.namedItem(key)?.value.trim()||'';
    const lines=[t('fmt-header'),'',t('fmt-name')+': '+value('name'),t('fmt-phone')+': '+value('phone')];
    if(value('email')) lines.push(t('fmt-email')+': '+value('email'));
    lines.push(t('fmt-subject')+': '+value('topic'),t('fmt-message')+': '+value('message'));
    return lines.join('\n');
  }
  async function copyText(text){
    try{ if(navigator.clipboard?.writeText){await navigator.clipboard.writeText(text);return true;} }
    catch(e){}
    try{resultText.focus();resultText.select();return document.execCommand('copy')===true;}
    catch(e){return false;}
  }
  form.addEventListener('submit',async event=>{
    event.preventDefault();
    if(!form.reportValidity())return;
    const phone=form.elements.namedItem('phone');
    if(numberOfDigits(phone.value)<8){phone.setCustomValidity(form.dataset.errPhone||'Invalid phone');phone.reportValidity();return;}
    phone.setCustomValidity('');
    const text=buildMessage();
    resultText.value=text;result.hidden=false;
    const copied=await copyText(text);
    status.textContent=form.dataset[copied?'copied':'manual']||form.dataset.ready||'';
    result.scrollIntoView({behavior:window.matchMedia('(prefers-reduced-motion:reduce)').matches?'auto':'smooth',block:'nearest'});
  });
  const phone=form.elements.namedItem('phone');
  phone?.addEventListener('input',()=>phone.setCustomValidity(''));
  copyAgain?.addEventListener('click',async()=>{
    if(!resultText.value)return;
    status.textContent=form.dataset[(await copyText(resultText.value))?'copied':'manual']||'';
  });
})();

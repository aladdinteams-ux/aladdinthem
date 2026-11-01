// V31: inquiry copy helper — demo only, never stores or sends data.
(()=>{document.querySelectorAll('[data-inquiry-form]').forEach(form=>{
 const feedback=form.querySelector('.v31-inquiry-feedback');
 const out=form.querySelector('.v31-request-preview');
 const ta=out?.querySelector('textarea');
 form.addEventListener('submit',async e=>{e.preventDefault();if(!form.reportValidity())return;
  const d=new FormData(form);const l=[form.dataset.msgTitle,form.dataset.msgUsage+': '+d.get('usage'),form.dataset.msgTopic+': '+d.get('topic')];
  if(String(d.get('note')||'').trim())l.push(form.dataset.msgNote+': '+String(d.get('note')).trim());
  const request=l.join('\n');if(out)out.hidden=false;if(ta)ta.value=request;
  try{if(navigator.clipboard?.writeText){await navigator.clipboard.writeText(request);feedback.textContent=form.dataset.copySuccess;}
      else{feedback.textContent=form.dataset.copyFail;}}
  catch(_){feedback.textContent=form.dataset.copyFail;}
 });
});})();

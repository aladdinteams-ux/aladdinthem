/* V33: progressively enhanced select menus; original select keeps form values and validity */
(()=>{
 const init=()=>{
  const selects=[...document.querySelectorAll('.psa-contact-form select, .v31-inquiry-form select')];
  let opened=null;
  const close=(wrap,returnFocus=false)=>{
    if(!wrap)return;
    const button=wrap.querySelector('.psa-pretty-trigger');
    const list=wrap.querySelector('.psa-pretty-menu');
    list.hidden=true;wrap.classList.remove('is-open');button.setAttribute('aria-expanded','false');
    if(opened===wrap)opened=null;
    if(returnFocus)button.focus();
  };
  selects.forEach((select,idx)=>{
    if(select.dataset.psaPrettyReady)return;
    select.dataset.psaPrettyReady='1';
    const label=select.closest('label');
    const caption=label?.querySelector(':scope > span');
    const wrap=document.createElement('div');wrap.className='psa-pretty-select';
    const button=document.createElement('button');button.type='button';button.className='psa-pretty-trigger';
    button.setAttribute('role','combobox');button.setAttribute('aria-haspopup','listbox');
    button.setAttribute('aria-expanded','false');
    const listId='psa-select-list-'+idx;
    const labelId='psa-select-label-'+idx;
    if(caption){caption.id=caption.id||labelId;button.setAttribute('aria-labelledby',caption.id+' psa-select-value-'+idx)}
    else{button.setAttribute('aria-label',select.getAttribute('aria-label')||select.name||'Select')}
    button.setAttribute('aria-controls',listId);
    button.innerHTML='<span class="psa-pretty-label" id="psa-select-value-'+idx+'"></span><svg class="psa-pretty-chevron" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 9 7 7 7-7"/></svg>';
    const valueEl=button.querySelector('.psa-pretty-label');
    const list=document.createElement('div');list.className='psa-pretty-menu';list.id=listId;
    list.setAttribute('role','listbox');list.hidden=true;
    const options=[...select.options];
    const entries=[];
    options.forEach((opt,i)=>{
      if(opt.disabled)return;
      const option=document.createElement('button');option.type='button';
      option.className='psa-pretty-option';option.setAttribute('role','option');
      option.setAttribute('aria-selected','false');option.tabIndex=-1;
      const title=document.createElement('span');title.textContent=opt.textContent;
      option.appendChild(title);
      option.insertAdjacentHTML('beforeend','<svg aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg>');
      list.appendChild(option);entries.push({value:opt.value,node:option});
      option.addEventListener('click',e=>{e.preventDefault();select.value=opt.value;
        select.dispatchEvent(new Event('input',{bubbles:true}));select.dispatchEvent(new Event('change',{bubbles:true}));
        sync();wrap.classList.remove('is-invalid');close(wrap,true);
      });
      option.addEventListener('keydown',e=>{
        if(e.key==='Escape'){e.preventDefault();close(wrap,true);return;}
        if(e.key==='Enter'||e.key===' '){e.preventDefault();option.click();return;}
        if(['ArrowDown','ArrowUp','Home','End'].includes(e.key)){
          e.preventDefault();const pos=entries.findIndex(t=>t.node===option);
          const next=e.key==='Home'?0:e.key==='End'?entries.length-1:(pos+(e.key==='ArrowDown'?1:-1)+entries.length)%entries.length;
          entries[next]?.node.focus();
        }
      });
    });
    const sync=()=>{
      const opt=select.selectedOptions[0]||select.options[0];
      valueEl.textContent=opt?.textContent||'';
      button.classList.toggle('is-empty',!select.value);
      entries.forEach(o=>o.node.setAttribute('aria-selected',String(o.value===select.value)));
      wrap.classList.remove('is-invalid');
    };
    const open=()=>{
      if(opened && opened!==wrap)close(opened);
      opened=wrap;wrap.classList.add('is-open');list.hidden=false;
      button.setAttribute('aria-expanded','true');
      list.classList.remove('is-up');
      const rect=button.getBoundingClientRect();
      const menuHeight=Math.min(254,entries.length*44+18);
      if(window.innerHeight-rect.bottom<menuHeight+135 && rect.top>menuHeight+24)list.classList.add('is-up');
      const current=entries.findIndex(o=>o.value===select.value);
      (entries[Math.max(0,current)]?.node)?.focus();
    };
    button.addEventListener('click',e=>{e.preventDefault();if(list.hidden)open();else close(wrap,true)});
    button.addEventListener('keydown',e=>{if(['ArrowDown','ArrowUp',' ','Enter'].includes(e.key)){
      e.preventDefault();if(list.hidden)open();else if(e.key==='Enter'||e.key===' ')close(wrap,true);
    }else if(e.key==='Escape')close(wrap,true)});
    select.addEventListener('change',sync);
    select.addEventListener('invalid',e=>{e.preventDefault();wrap.classList.add('is-invalid');button.focus()});
    wrap.append(button,list);select.classList.add('psa-native-select');select.tabIndex=-1;
    select.after(wrap);sync();
  });
  document.addEventListener('pointerdown',e=>{if(opened&&!opened.contains(e.target))close(opened)});
  document.addEventListener('keydown',e=>{if(e.key==='Escape'&&opened){e.preventDefault();close(opened,true)}});
  document.addEventListener('focusin',e=>{if(opened&&!opened.contains(e.target))close(opened)});
  window.addEventListener('resize',()=>{if(opened)close(opened)});
 };
 if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init,{once:true});else init();
})();

const { chromium } = require('/opt/node22/lib/node_modules/playwright');
(async()=>{
 const B='http://127.0.0.1:8080';
 const pages=JSON.parse(process.argv[2]); // [[name,url]]
 const browser=await chromium.launch({executablePath:'/opt/pw-browsers/chromium'});
 const res=[];
 for(const vp of [[390,844,'mobile'],[820,1180,'tablet'],[1440,900,'desktop']]){
  const ctx=await browser.newContext({viewport:{width:vp[0],height:vp[1]},locale:'fa-IR'});
  for(const [name,url] of pages){
   const page=await ctx.newPage();const errs=[];const failed=[];
   page.on('console',m=>{if(m.type()==='error')errs.push(m.text().slice(0,160)+' @'+(m.location().url||'').replace(B,'').slice(0,80));});
   page.on('pageerror',e=>errs.push('PAGEERROR '+e.message.slice(0,160)));
   page.on('requestfailed',r=>failed.push(r.url().replace(B,'').slice(0,100)));
   page.on('response',r=>{if(r.status()>=400)failed.push(r.status()+' '+r.url().replace(B,'').slice(0,100));});
   await page.goto(B+url,{waitUntil:'load',timeout:60000}).catch(e=>errs.push('NAV '+e.message));
   await page.waitForTimeout(600);
   const m=await page.evaluate(()=>({sw:document.documentElement.scrollWidth,cw:document.documentElement.clientWidth,h:document.documentElement.scrollHeight}));
   if(vp[2]==='mobile'&&name==='home')await page.screenshot({path:`/tmp/claude-0/pw/${name}-${vp[2]}.png`,fullPage:false});
   res.push({vp:vp[2],name,overflowX:m.sw>m.cw+1,sw:m.sw,cw:m.cw,errs:[...new Set(errs)],failed:[...new Set(failed)]});
   await page.close();
  }
  await ctx.close();
 }
 await browser.close();
 for(const r of res){console.log(`${r.vp}/${r.name}: overflowX=${r.overflowX}(${r.sw}/${r.cw}) errors=${r.errs.length} failed=${r.failed.length}`);
  r.errs.filter(e=>!/elementor\/assets|gravatar|status of 404 \(Not Found\) @\/\?p=/.test(e)).forEach(e=>console.log('   ERR',e));r.failed.slice(0,4).forEach(e=>console.log('   FAIL',e));}
})();

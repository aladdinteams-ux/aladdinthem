const { chromium } = require('/opt/node22/lib/node_modules/playwright');
(async()=>{const b=await chromium.launch({executablePath:'/opt/pw-browsers/chromium'});const p=await (await b.newContext({viewport:{width:390,height:844}})).newPage();
await p.goto('http://127.0.0.1:8080'+process.argv[2],{waitUntil:'load'});await p.waitForTimeout(500);
console.log(await p.evaluate(()=>{const vw=document.documentElement.clientWidth;const out=[];document.querySelectorAll('body *').forEach(e=>{const r=e.getBoundingClientRect();if(r.right>vw+1||r.left<-1){out.push(e.tagName+'.'+(e.className&&e.className.baseVal===undefined?e.className:'')+' L'+Math.round(r.left)+' R'+Math.round(r.right)+' w'+Math.round(r.width));}});return out.slice(0,12).join('\n');}));await b.close();})();

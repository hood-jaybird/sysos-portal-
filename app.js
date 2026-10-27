const $=(s,e=document)=>e.querySelector(s),$$=(s,e=document)=>[...e.querySelectorAll(s)];
const esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const api=(a,q='',o)=>fetch('api.php?a='+a+q,o).then(r=>r.json());
const post=(a,d)=>api(a,'',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(d)});
const toast=t=>{const e=$('#toast');e.textContent=t;e.classList.add('s');setTimeout(()=>e.classList.remove('s'),2200)};
const pills=(id,items,fn)=>{const e=$(id);e.innerHTML=items.map(k=>`<button>${k}</button>`).join('');$$('button',e).forEach(b=>b.onclick=()=>{$$('button',e).forEach(x=>x.classList.remove('on'));b.classList.add('on');fn(b.textContent)})};
/* revelado y menú activo */
const io=new IntersectionObserver(es=>es.forEach(e=>e.isIntersecting&&e.target.classList.add('in')),{threshold:.1});
$$('.rv').forEach(e=>io.observe(e));
addEventListener('scroll',()=>{let c='#inicio';$$('header[id],section[id]').forEach(s=>{if(s.getBoundingClientRect().top<200)c='#'+s.id});$$('#menu a').forEach(a=>a.classList.toggle('on',a.getAttribute('href')==c))});
/* máquina de escribir */
const W=['planificación de procesos','memoria virtual','inodos y VFS','namespaces y cgroups'];let wi=0,ci=0,del=0;
setInterval(()=>{const w=W[wi];if(!del){ci++;if(ci>w.length+12){del=1}}else{ci--;if(ci<=0){del=0;wi=(wi+1)%W.length}}$('#type').textContent=w.slice(0,Math.max(0,ci))},90);
/* procesos */
const S={Nuevo:'Proceso recién creado: el SO le arma su PCB.',Listo:'En la cola esperando CPU; el planificador (RR, SJF, prioridades) decide.',Ejecución:'Usa la CPU hasta agotar su quantum, pedir E/S o terminar.',Bloqueado:'Espera una E/S o un semáforo/mutex.',Terminado:'Libera recursos; es zombie hasta que el padre llama wait().'};
let si=0;const stB=()=>$$('#st button');
const show=k=>$('#stInfo').textContent=S[k];
pills('#st',Object.keys(S),k=>{show(k);clearInterval(auto)});
const auto=setInterval(()=>{const b=stB()[si++%5];stB().forEach(x=>x.classList.remove('on'));b.classList.add('on');show(b.textContent)},2200);
let buf=[0,0,0,0,0];const drawB=()=>$('#buf').innerHTML=buf.map(x=>`<i class="${x?'f':''}"></i>`).join(''),msg=t=>$('#pcMsg').textContent=t;drawB();
$('#prod').onclick=()=>{const i=buf.indexOf(0);if(i<0)return msg('Buffer lleno: el productor se BLOQUEA en sem_wait(vacíos).');buf[i]=1;drawB();msg('Producido: mutex tomado, dato guardado, mutex liberado.')};
$('#cons').onclick=()=>{const i=buf.lastIndexOf(1);if(i<0)return msg('Buffer vacío: el consumidor se BLOQUEA en sem_wait(llenos).');buf[i]=0;drawB();msg('Consumido: se libera un espacio con sem_post(vacíos).')};
/* memoria */
const PT={0:5,1:2,2:7,3:1};
function mmu(){const s=+$('#ps').value,d=+$('#la').value||0,p=Math.floor(d/s),o=d%s,f=PT[p];
$('#mmuOut').innerHTML=f==null?`Página ${p}, desplazamiento ${o}: <b class="bad">PAGE FAULT</b>. El SO carga la página desde disco y reinicia la instrucción.`:`Página ${p}, desplazamiento ${o} → marco ${f}.<br>Física = ${f}×${s}+${o} = <b class="ok">${f*s+o}</b>`}
function rep(r,n,alg){let m=[],f=0;r.forEach((x,i)=>{const h=m.indexOf(x);if(h>=0){if(alg=='LRU'){m.splice(h,1);m.push(x)}return}f++;if(m.length>=n){let v=0;if(alg=='Óptimo'){let far=-1;m.forEach((y,j)=>{let k=r.indexOf(y,i+1);if(k<0)k=1e9;if(k>far){far=k;v=j}})}m.splice(v,1)}m.push(x)});return f}
function repAll(){const r=$('#refs').value.trim().split(/\s+/).filter(Boolean),n=Math.max(1,+$('#fr').value||1);
$('#repOut').innerHTML=['FIFO','LRU','Óptimo'].map(a=>{const f=rep(r,n,a);return`${a}: ${f} fallos<div class="bar"><i style="--w:${f/r.length*100}%;background:var(--a);width:${f/r.length*100}%"></i></div>`}).join('')}
['ps','la'].forEach(i=>$('#'+i).oninput=mmu);['refs','fr'].forEach(i=>$('#'+i).oninput=repAll);mmu();repAll();
/* archivos */
const T={'/etc':'Configuración del sistema: hosts, passwd, apache2/.','/var':'Datos variables: logs en /var/log, web en /var/www.','/proc':'FS virtual del kernel: meminfo, cpuinfo y un directorio por PID. No ocupa disco.','/dev':'Dispositivos como archivos: /dev/sda, /dev/null, /dev/tty.'};
pills('#tree',Object.keys(T),k=>$('#treeInfo').textContent=T[k]);
/* dashboard */
const ring=(id,p,t)=>{const e=$(id);$('.r',e).style.strokeDashoffset=314-314*Math.min(p,100)/100;$('b',e).textContent=Math.round(p)+'%';$('small',e).textContent=t};
async function tick(){try{const d=await api('metrics');$('#live').className='dot';
const lp=d.load[0]/d.cores*100,ru=d.ram?d.ram.used/d.ram.total*100:0,up=d.uptime||'no disponible';
ring('#rCpu',lp,`carga ${d.load.map(x=>x.toFixed(2)).join(' · ')} (1/5/15 min)`);
ring('#rRam',ru,d.ram?`${d.ram.used}/${d.ram.total} MB · libre ${d.ram.free} · disp. ${d.ram.avail}`:'sin acceso a /proc/meminfo');
ring('#rDsk',d.disk.pct,`${d.disk.used}/${d.disk.size} en ${d.disk.mount} (${d.disk.fs})`);
$('#upTxt').textContent=up;$('#hUp').textContent=up.replace('up ','');$('#hLoad').textContent=d.load[0].toFixed(2);$('#hRam').textContent=Math.round(ru)+'%';
$('#procs').innerHTML=d.procs.map(p=>`<tr><td>${p.pid}</td><td>${esc(p.user)}</td><td>${p.cpu}</td><td>${p.mem}</td><td title="${esc(p.cmd)}">${esc(p.cmd)}</td></tr>`).join('')}catch(e){$('#live').className='dot off'}}
tick();setInterval(tick,3000);
/* CRUD */
let flt='',cache=[];
pills('#flt',['todos','procesos','memoria','archivos','virtualizacion'],k=>{flt=k=='todos'?'':k;load()});
async function load(){const r=await api('list','&m='+flt+'&q='+encodeURIComponent($('#q').value));
if(r.error){$('#list').innerHTML=`<p>${esc(r.error)}</p>`;return}cache=r;
$('#list').innerHTML=r.map(c=>`<article class="card"><small>${c.modulo}</small><h4>${esc(c.titulo)}</h4><p>${esc(c.descripcion)}</p><div><button data-a="fav" data-id="${c.id}">${c.fav==1?'★':'☆'}</button><button data-a="ed" data-id="${c.id}">Editar</button><button data-a="del" data-id="${c.id}">Eliminar</button></div></article>`).join('')||'<p>Aún no hay conceptos. Crea el primero con el formulario.</p>'}
$('#q').oninput=load;
$('#list').onclick=async e=>{const b=e.target.closest('button');if(!b)return;const id=b.dataset.id,c=cache.find(x=>x.id==id);
if(b.dataset.a=='fav'){await post('fav',{id});load()}
if(b.dataset.a=='del'&&confirm('¿Eliminar "'+c.titulo+'"?')){await post('del',{id});toast('Concepto eliminado');load()}
if(b.dataset.a=='ed'){$('#id').value=id;$('#mod').value=c.modulo;$('#tit').value=c.titulo;$('#des').value=c.descripcion;$('#fT').textContent='Editando concepto';$('#f').scrollIntoView({behavior:'smooth',block:'center'})}};
const reset=()=>{$('#f').reset();$('#id').value='';$('#fT').textContent='Nuevo concepto'};$('#cancel').onclick=reset;
$('#f').onsubmit=async e=>{e.preventDefault();const r=await post('save',{id:$('#id').value,modulo:$('#mod').value,titulo:$('#tit').value,descripcion:$('#des').value});
if(r.error)return toast(r.error);toast('Concepto guardado');reset();load()};
load();

/* Round Robin animado */
const col=['#8b5cf6','#fb923c','#22d3ee','#eab308','#f43f5e','#22c55e'];let rrT;
$('#rrGo').onclick=()=>{clearInterval(rrT);
const b=$('#rrB').value.trim().split(/\s+/).map(Number).filter(n=>n>0).slice(0,6),q=Math.max(1,+$('#rrQ').value||1);
if(!b.length)return toast('Escribe ráfagas, por ejemplo: 5 3 8');
let rem=[...b],w=b.map((_,i)=>i),t=0,g=[],fin=[];
while(w.length){const i=w.shift(),r=Math.min(q,rem[i]);g.push([i,t,r,[...w]]);t+=r;rem[i]-=r;rem[i]>0?w.push(i):fin[i]=t}
$('#rrG').innerHTML='';$('#rrO').textContent='';let k=0;
rrT=setInterval(()=>{if(k>=g.length){clearInterval(rrT);const e=fin.map((f,i)=>f-b[i]);
$('#rrC').textContent='CPU libre (tiempo total '+t+')';$('#rrC').style.background='#ddd';$('#rrQ2').textContent='—';
$('#rrO').innerHTML='Espera: '+e.map((x,i)=>`P${i+1}=${x}`).join(' · ')+` → promedio <b>${(e.reduce((x,y)=>x+y)/e.length).toFixed(2)}</b>`;return}
const[i,s,r,qs]=g[k++];$('#rrC').textContent='CPU ejecuta P'+(i+1)+' ('+r+' u.)';$('#rrC').style.background=col[i];
$('#rrQ2').innerHTML=qs.map(j=>`<i style="background:${col[j]}">P${j+1}</i>`).join('')||'—';
$('#rrG').insertAdjacentHTML('beforeend',`<em style="background:${col[i]};width:${r*30}px">P${i+1}<small>${s}</small></em>`)},800)};
/* Filósofos comensales */
let P,F,safe=1,dpT;
function dpDraw(){const dead=P.every(p=>p.s=='h'&&p.a);$('#tbl').className=dead?'dead':'';
$('#tbl').innerHTML=P.map((p,i)=>{const a=i/5*6.2832-1.5708;return`<b class="ph ${p.s}" style="left:${50+40*Math.cos(a)}%;top:${50+40*Math.sin(a)}%">${i+1}<small>${{p:'piensa',h:'hambre',e:'come'}[p.s]}</small></b>`}).join('')+
F.map((o,i)=>{const a=(i+.5)/5*6.2832-1.5708;return`<span class="fk" style="left:${50+22*Math.cos(a)}%;top:${50+22*Math.sin(a)}%;${o>=0?`background:${col[o]};opacity:1`:''}">🍴</span>`}).join('');
if(dead){clearInterval(dpT);$('#dpMsg').innerHTML='<b class="bad">DEADLOCK:</b> cada filósofo tiene un tenedor y espera el otro, en círculo. Nadie avanza.'}}
function dpStep(){P.forEach((p,i)=>{const l=(i+4)%5,r=i,a=safe?Math.min(l,r):l,b=safe?Math.max(l,r):r;
if(p.s=='p'){if(Math.random()<(safe?.35:.7))p.s='h'}
else if(p.s=='h'){if(!p.a&&F[a]<0){F[a]=i;p.a=1}else if(p.a&&F[b]<0){F[b]=i;p.s='e';p.t=2+Math.floor(Math.random()*2)}}
else if(--p.t<=0){F[l]=F[r]=-1;p.s='p';p.a=0}});dpDraw()}
function dpInit(){clearInterval(dpT);P=[0,1,2,3,4].map(()=>({s:'p',a:0,t:0}));F=[-1,-1,-1,-1,-1];
$('#dpMode').textContent=safe?'Provocar deadlock':'Usar orden de tenedores';
$('#dpMsg').textContent=safe?'Cada hilo toma primero el tenedor de menor número (jerarquía de recursos): no hay espera circular.':'Cada filósofo toma primero su tenedor izquierdo; mira si se bloquean.';
dpDraw();dpT=setInterval(dpStep,900)}
$('#dpMode').onclick=()=>{safe=!safe;dpInit()};$('#dpReset').onclick=dpInit;dpInit();

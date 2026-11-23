document.addEventListener('DOMContentLoaded',()=>{
 const toggle=document.getElementById('themeToggle');
 const saved=localStorage.getItem('pos-theme');if(saved==='light')document.body.classList.add('theme-light');
 if(toggle)toggle.addEventListener('click',()=>{document.body.classList.toggle('theme-light');localStorage.setItem('pos-theme',document.body.classList.contains('theme-light')?'light':'dark');});
 const chart=document.getElementById('salesChart');if(chart&&window.Chart){new Chart(chart,{type:'line',data:{labels:window.salesLabels||[],datasets:[{label:'Penjualan',data:window.salesValues||[],borderColor:'#5594ff',backgroundColor:'rgba(85,148,255,.14)',fill:true,tension:.35,pointRadius:3}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{x:{grid:{display:false},ticks:{color:'#8fa1bc'}},y:{beginAtZero:true,grid:{color:'rgba(130,150,180,.12)'},ticks:{color:'#8fa1bc',callback:v=>'Rp '+Number(v).toLocaleString('id-ID')}}}}});}
 const grid=document.getElementById('productGrid'), search=document.getElementById('productSearch');
 if(grid&&search)search.addEventListener('input',()=>{const q=search.value.toLowerCase();grid.querySelectorAll('.product-tile').forEach(b=>b.hidden=!(b.dataset.name.toLowerCase().includes(q)||b.dataset.code.toLowerCase().includes(q)));});
 if(!grid||!window.posProducts)return;
 const cart=new Map(), itemsEl=document.getElementById('cartItems'), hidden=document.getElementById('hiddenItems'), totalEl=document.getElementById('cartTotal'), countEl=document.getElementById('cartCount'), payment=document.getElementById('paymentInput'), changeEl=document.getElementById('changeAmount'), checkout=document.getElementById('checkoutBtn');
 const money=n=>'Rp '+Number(n).toLocaleString('id-ID',{maximumFractionDigits:0});
 function render(){let total=0,count=0;itemsEl.innerHTML='';hidden.innerHTML='';if(!cart.size)itemsEl.innerHTML='<div class="empty-cart"><i class="fa-solid fa-basket-shopping"></i><p>Keranjang masih kosong</p></div>';
  for(const [id,it] of cart){count+=it.qty;total+=it.price*it.qty;const row=document.createElement('div');row.className='cart-row';row.innerHTML=`<div><strong>${escapeHtml(it.name)}</strong><small>${money(it.price)} / unit</small><div class="qty-controls"><button type="button" data-act="minus" data-id="${id}">−</button><span>${it.qty}</span><button type="button" data-act="plus" data-id="${id}">+</button><button type="button" data-act="remove" data-id="${id}" aria-label="Hapus">×</button></div></div><div class="line-total">${money(it.price*it.qty)}</div>`;itemsEl.appendChild(row);
   const a=document.createElement('input');a.type='hidden';a.name=`items[${id}][id]`;a.value=id;hidden.appendChild(a);const b=document.createElement('input');b.type='hidden';b.name=`items[${id}][qty]`;b.value=it.qty;hidden.appendChild(b);
  }
  totalEl.textContent=money(total);countEl.textContent=count+' unit';const paid=Number(payment.value||0);changeEl.textContent=money(Math.max(0,paid-total));checkout.disabled=!cart.size||paid<total;
 }
 function escapeHtml(s){return String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));}
 grid.addEventListener('click',e=>{const btn=e.target.closest('.product-tile');if(!btn)return;const id=Number(btn.dataset.id),p=window.posProducts.find(x=>x.id===id);if(!p)return;const it=cart.get(id)||{...p,qty:0};if(it.qty>=p.stock){alert('Jumlah melebihi stok yang tersedia.');return;}it.qty++;cart.set(id,it);render();});
 itemsEl.addEventListener('click',e=>{const b=e.target.closest('button[data-act]');if(!b)return;const id=Number(b.dataset.id),it=cart.get(id);if(!it)return;if(b.dataset.act==='plus'){if(it.qty>=it.stock){alert('Jumlah melebihi stok.');return;}it.qty++;}else if(b.dataset.act==='minus'){it.qty--;if(it.qty<=0)cart.delete(id);}else cart.delete(id);render();});
 document.getElementById('clearCart')?.addEventListener('click',()=>{cart.clear();render();});payment?.addEventListener('input',render);document.getElementById('checkoutForm')?.addEventListener('submit',e=>{if(!cart.size||Number(payment.value)<[...cart.values()].reduce((s,i)=>s+i.price*i.qty,0)){e.preventDefault();alert('Periksa keranjang dan nominal pembayaran.');}});
 render();
});

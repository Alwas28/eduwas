<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
function openModal(id){document.getElementById(id).classList.add('modal-open');document.body.style.overflow='hidden'}
function closeModal(id){document.getElementById(id).classList.remove('modal-open');document.body.style.overflow='';clearErrors(id)}
document.querySelectorAll('.modal-backdrop').forEach(el=>{
  el.addEventListener('click',function(e){if(e.target===this)closeModal(this.id)})
});
function showToast(type,message){
  const c=document.getElementById('toast-container'),ok=type==='success';
  const el=document.createElement('div');el.className='toast';
  el.innerHTML=`<div class="toast-icon" style="background:${ok?'rgba(16,185,129,.15)':'rgba(248,113,113,.15)'};color:${ok?'#34d399':'#f87171'}"><i class="fa-solid ${ok?'fa-circle-check':'fa-circle-xmark'}"></i></div><div class="flex-1 min-w-0"><p style="font-size:13.5px;font-weight:600;color:var(--text)">${ok?'Berhasil':'Gagal'}</p><p style="font-size:12px;color:var(--muted);margin-top:2px">${message}</p></div><button onclick="dismissToast(this.closest('.toast'))" style="color:var(--muted);font-size:13px;padding:4px;flex-shrink:0"><i class="fa-solid fa-xmark"></i></button>`;
  c.appendChild(el);setTimeout(()=>dismissToast(el),4000);
}
function dismissToast(el){if(!el||el.classList.contains('toast-out'))return;el.classList.add('toast-out');setTimeout(()=>el.remove(),300)}
function showErrors(prefix,errors){Object.keys(errors).forEach(f=>{const el=document.getElementById(`err-${prefix}-${f}`);if(el){el.textContent=errors[f][0];el.classList.remove('hidden')}})}
function clearErrors(modalId){document.querySelectorAll(`#${modalId} .f-error`).forEach(el=>{el.textContent='';el.classList.add('hidden')})}
function setLoading(btnId,state){const b=document.getElementById(btnId);b.disabled=state;b.style.opacity=state?'.6':'1'}
const DT_LANG={search:'',searchPlaceholder:'Cari...',lengthMenu:'Tampilkan _MENU_ data',info:'Menampilkan _START_–_END_ dari _TOTAL_ data',infoEmpty:'Tidak ada data',infoFiltered:'(difilter dari _MAX_ total)',paginate:{previous:'<i class="fa-solid fa-chevron-left"></i>',next:'<i class="fa-solid fa-chevron-right"></i>'},emptyTable:'Tidak ada data',zeroRecords:'Tidak ditemukan data yang cocok'};
const DT_DOM='<"flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between mb-4"lf>rt<"flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between mt-4"ip>';
</script>

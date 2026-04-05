<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
.dataTables_wrapper{color:var(--text)}
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input{background:var(--surface2);border:1px solid var(--border);color:var(--text);border-radius:8px;padding:6px 10px;outline:none;font-size:13px;font-family:'Plus Jakarta Sans',sans-serif}
.dataTables_wrapper .dataTables_filter input:focus{border-color:var(--ac)}
.dataTables_wrapper .dataTables_length label,
.dataTables_wrapper .dataTables_filter label{color:var(--muted);font-size:13px;gap:8px;display:flex;align-items:center}
table.dataTable thead th{background:var(--surface2)!important;color:var(--muted)!important;font-size:10.5px!important;font-weight:600!important;letter-spacing:.7px!important;text-transform:uppercase!important;border-bottom:1px solid var(--border)!important;padding:12px 16px!important;white-space:nowrap}
table.dataTable tbody td{padding:12px 16px!important;border-bottom:1px solid var(--border)!important;font-size:13px;color:var(--text);vertical-align:middle}
table.dataTable tbody tr{background:var(--surface)!important;transition:background .15s}
table.dataTable tbody tr:hover{background:var(--card-hover)!important}
table.dataTable tbody tr:last-child td{border-bottom:none!important}
table.dataTable.no-footer{border:none!important}
table.dataTable thead .sorting::after{content:' ↕';opacity:.3}
table.dataTable thead .sorting_asc::after{content:' ↑';color:var(--ac)}
table.dataTable thead .sorting_desc::after{content:' ↓';color:var(--ac)}
table.dataTable thead .sorting,table.dataTable thead .sorting_asc,table.dataTable thead .sorting_desc{background-image:none!important;cursor:pointer}
.dataTables_wrapper .dataTables_info{color:var(--muted);font-size:12px}
.dataTables_wrapper .dataTables_paginate .paginate_button{background:transparent!important;border:1px solid var(--border)!important;color:var(--sub)!important;border-radius:8px!important;padding:4px 10px!important;font-size:12px!important;margin:0 2px!important;cursor:pointer;transition:all .15s}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover{background:var(--surface2)!important;border-color:var(--ac)!important;color:var(--text)!important}
.dataTables_wrapper .dataTables_paginate .paginate_button.current{background:var(--ac)!important;border-color:var(--ac)!important;color:#fff!important}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled{opacity:.4;cursor:default}

/* Modal */
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.55);backdrop-filter:blur(4px);z-index:200;display:none;align-items:center;justify-content:center;padding:16px}
.modal-backdrop.modal-open{display:flex}
.modal-box{background:var(--surface);border:1px solid var(--border);border-radius:20px;width:100%;max-width:480px;box-shadow:0 24px 60px rgba(0,0,0,.4);animation:fadeUp .25s ease both}
.modal-box.modal-sm{max-width:380px}
.modal-box.modal-lg{max-width:600px}

/* Form */
.f-input{width:100%;background:var(--surface2);border:1px solid var(--border);color:var(--text);border-radius:10px;padding:9px 12px;font-size:13.5px;font-family:'Plus Jakarta Sans',sans-serif;outline:none;transition:border-color .15s}
.f-input:focus{border-color:var(--ac)}
.f-input::placeholder{color:var(--muted)}
.f-label{font-size:12.5px;font-weight:600;color:var(--sub);margin-bottom:6px;display:block}
.f-error{font-size:11.5px;color:#f87171;margin-top:4px}
.f-hint{font-size:11.5px;margin-top:4px;color:var(--muted)}

/* Checkbox custom */
.cb-item{display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:10px;cursor:pointer;transition:background .15s;border:1px solid var(--border)}
.cb-item:hover{background:var(--surface2)}
.cb-item input[type=checkbox]{width:16px;height:16px;accent-color:var(--ac);cursor:pointer;flex-shrink:0}
.cb-item.checked{background:var(--ac-lt);border-color:rgba(var(--ac-rgb),.3)}

/* Toast */
.toast-wrap{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none}
.toast{display:flex;align-items:center;gap:12px;background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:14px 18px;min-width:280px;max-width:360px;box-shadow:0 8px 32px rgba(0,0,0,.3);pointer-events:all;animation:slideIn .3s ease both}
.toast.toast-out{animation:slideOut .3s ease forwards}
.toast-icon{width:36px;height:36px;border-radius:10px;display:grid;place-items:center;font-size:15px;flex-shrink:0}
@keyframes slideIn{from{opacity:0;transform:translateX(40px)}to{opacity:1;transform:translateX(0)}}
@keyframes slideOut{to{opacity:0;transform:translateX(40px)}}
</style>

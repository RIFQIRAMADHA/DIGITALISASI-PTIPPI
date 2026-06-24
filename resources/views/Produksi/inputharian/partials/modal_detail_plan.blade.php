<div class="modal fade" id="detailPlanModal" tabindex="-1" aria-labelledby="detailPlanModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 98%; z-index: 1060;">
        <div class="modal-content" style="border: 2px solid #e11d2e; border-radius: 12px; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.6);">
            
            <div class="modal-header d-flex justify-content-between align-items-center" style="background: #e11d2e; border-bottom: 1px solid rgba(255,255,255,0.3); padding: 12px 25px;">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-list-alt text-white" style="font-size: 20px;"></i>
                    <h5 class="modal-title text-white font-weight-bold" id="detailPlanModalLabel" style="letter-spacing: 1px; color: #ffffff !important;">
                        DATA LENGKAP PLAN SCHEDULE PRODUKSI
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1); opacity: 1;"></button>
            </div>
            
            <div class="modal-body" style="background: #f1f2f6; padding: 20px; max-height: 75vh; overflow-y: auto;">
                
                <div id="loadingDetailPlan" class="text-center py-5">
                    <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;"></div>
                    <h5 class="mt-3 text-muted fw-bold">MENARIK DATA LENGKAP...</h5>
                </div>

                <div id="contentDetailPlan" class="table-responsive" style="display: none; border-radius: 8px; border: 1px solid #ffffff;">
                    <table class="table table-bordered table-hover mb-0 text-center" id="tableDetailPlanAll" style="font-size: 10.5px; min-width: 1800px; background: white;">
                        <thead class="text-white">
                            <tr style="border: none;">
                                <th colspan="3" style="background: #e11d2e; border: 1px solid #ffffff;">ITEM INFO</th>
                                <th colspan="3" style="background: #e11d2e; border: 1px solid #ffffff;">TARGET QTY</th>
                                <th colspan="7" style="background: #e11d2e; border: 1px solid #ffffff;">TIMING & PERFORMANCE</th>
                                <th colspan="2" style="background: #e11d2e; border: 1px solid #ffffff;">MACHINERY</th>
                                <th colspan="2" style="background: #e11d2e; border: 1px solid #ffffff;">LOGISTICS</th>
                                <th rowspan="2" style="background: #e11d2e; vertical-align: middle; border: 1px solid #ffffff;">NOTE</th>
                            </tr>
                            <tr class="text-nowrap" style="background: #353b48;">
                                <th style="border: 1px solid #ffffff;">JOB NO</th>
                                <th style="border: 1px solid #ffffff;">PART NAME</th>
                                <th style="border: 1px solid #ffffff;">PO NUMBER</th>
                                <th style="border: 1px solid #ffffff;">PLAN A</th>
                                <th style="border: 1px solid #ffffff;">PLAN B</th>
                                <th style="border: 1px solid #ffffff;">GSPH</th>
                                <th style="border: 1px solid #ffffff;">START</th>
                                <th style="border: 1px solid #ffffff;">FINISH</th>
                                <th style="border: 1px solid #ffffff;">TPT</th>
                                <th style="border: 1px solid #ffffff;">CT</th>
                                <th style="border: 1px solid #ffffff;">LOSS (UBP/DTR)</th>
                                <th style="border: 1px solid #ffffff;">WORKTIME</th>
                                <th style="border: 1px solid #ffffff;">STROKE</th>
                                <th style="border: 1px solid #ffffff;">MESIN (1-5)</th>
                                <th style="border: 1px solid #ffffff;">DIES HIGH</th>
                                <th style="background: #e11d2e; color: #ffffff; border: 1px solid #ffffff;">PALLET</th>
                                <th style="background: #e11d2e; color: #ffffff; border: 1px solid #ffffff;">MATERIAL</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDetailPlanAll" style="font-weight: 800; color: #000;">
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer" style="background: #f1f2f6; border-top: 1px solid #d1d5db; padding: 15px 25px;">
                <button type="button" class="btn btn-danger fw-bold px-5 shadow-sm" data-bs-dismiss="modal" style="border-radius: 6px; border: 1px solid #ffffff;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* 1. OVERLAY / BACKDROP UTAMA */
    #detailPlanModal {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        z-index: 1050 !important;
        background: rgba(0, 0, 0, 0.7) !important;
        overflow: hidden !important; 
        display: none; 
        align-items: flex-start;
        justify-content: center;
        padding-top: 60px; 
    }

    /* 2. DIALOG MODAL: Wadah Header, Body, Footer */
    #detailPlanModal .modal-dialog {
        margin: 0 auto !important;
        max-width: 96% !important;
        width: 96% !important;
        height: auto;
        display: block !important;
    }

    /* 3. MODAL CONTENT & BODY: Di sini tempat scroll-nya */
    #detailPlanModal .modal-content {
        border: 2px solid #e11d2e;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        max-height: 85vh; 
    }

    #detailPlanModal .modal-body {
        background: #f1f2f6; 
        padding: 0 !important; 
        overflow-y: auto !important; 
        overflow-x: auto !important; 
        position: relative;
        flex-grow: 1;
    }

    /* 4. STICKY HEADER TABEL (ANTI-TEMBUS & WARNA ASLI) */
    #tableDetailPlanAll {
        border-collapse: separate !important; 
        border-spacing: 0;
        width: 100%;
    }

    #tableDetailPlanAll thead th {
        position: sticky !important;
        z-index: 100;
        vertical-align: middle;
        text-align: center !important;
        border: 1px solid #ffffff !important;
        color: #ffffff !important;
    }

    #tableDetailPlanAll thead tr:nth-child(1) th {
        top: 0;
        background-color: #e11d2e !important; 
    }

    #tableDetailPlanAll thead tr:nth-child(2) th {
        top: 38px; 
        background-color: #e11d2e !important; 
    }

    /* 🔥 BIKIN KOLOM JOB NO STICKY KIRI */
    #tableDetailPlanAll thead tr:nth-child(2) th:nth-child(1) {
        left: 0;
        z-index: 150 !important; /* Harus lebih tinggi dari 100 biar numpuk di atas kolom lain pas scroll */
        background-color: #e11d2e !important; /* Paksa warna asli header biar gak transparan */
        box-shadow: 1px 0 0 #ffffff; /* Ganti border kanan dengan box-shadow supaya lebih rapi saat scroll */
    }

    #tableDetailPlanAll tbody td:nth-child(1) {
        position: sticky !important;
        left: 0;
        z-index: 50 !important; /* Lebih tinggi dari td biasa */
        background-color: #ffffff !important; /* Paksa putih solid supaya teks di bawahnya gak tembus */
        box-shadow: 1px 0 0 #dee2e6; /* Pengganti border-right supaya nempel mulus */
    }

    /* 5. BODY TABLE (PUTIH SOLID BIAR GAK TEMBUS) */
    #tableDetailPlanAll tbody td {
        background-color: #ffffff !important; 
        vertical-align: middle !important;
        text-align: center !important;
        padding: 10px 5px !important;
        border: 1px solid #dee2e6 !important;
        font-weight: 800;
        color: #000;
    }

    /* 🔥 FIX SAKTI: Paksa cell kolom mesin di modal agar isinya lurus sejajar ke samping & anti-patah */
    #tableDetailPlanAll tbody td:nth-last-child(4) {
        display: table-cell !important; 
        white-space: nowrap !important;  
        min-width: 130px !important;     
    }

    /* 6. STYLE BADGE MESIN (WARNA ASLI LU TETEP SAMA) */
    .badge-mesin { 
        display: inline-flex; 
        width: 18px; height: 18px; 
        align-items: center; justify-content: center;
        border-radius: 3px; 
        font-weight: 900; 
        font-size: 9px; 
        margin: 1px; 
        border: 1px solid #9ca3af;
        background-color: #d1d5db;
        box-sizing: border-box;
    }

    .bg-active-m { 
        color: #4e73df !important; 
        border: 1px solid #4e73df !important; 
        background-color: #ffffff !important;
    }

    /* Sembaring menyembunyikan icon check lama, kita siapkan jangkar buat teks buatan */
    .bg-inactive-m { 
        color: transparent !important; 
        background-color: #d1d5db !important;
        border: 1px solid #9ca3af !important;
    }

    /* ======================================================================
       🔥 TRIK SAKTI PSEUDO-ELEMENT: GENERATE ANGKA DINAMIS HANYA UNTUK YANG AKTIF
       ====================================================================== */
    
    /* Sembunyikan icon check bawaan script lama khusus di kotak yang aktif */
    .bg-active-m i, .bg-active-m font, .bg-active-m svg {
        display: none !important;
    }

    /* Hapus teks bawaan apa pun di dalam kotak aktif agar bersih */
    .bg-active-m {
        font-size: 0 !important;
    }

    /* Inject angka 1-5 murni lewat CSS berdasarkan urutan posisinya di dalam td */
    .badge-mesin.bg-active-m:nth-child(1)::before { content: "1"; font-size: 9px; font-weight: 900; }
    .badge-mesin.bg-active-m:nth-child(2)::before { content: "2"; font-size: 9px; font-weight: 900; }
    .badge-mesin.bg-active-m:nth-child(3)::before { content: "3"; font-size: 9px; font-weight: 900; }
    .badge-mesin.bg-active-m:nth-child(4)::before { content: "4"; font-size: 9px; font-weight: 900; }
    .badge-mesin.bg-active-m:nth-child(5)::before { content: "5"; font-size: 9px; font-weight: 900; }

    /* Fix tombol close agar selalu bisa diklik */
    .modal-footer {
        background: #ffffff;
        z-index: 1060;
    }

    .modal-backdrop { display: none !important; }

    /* Scrollbar Merah Custom */
    #detailPlanModal .modal-body::-webkit-scrollbar { width: 10px; height: 10px; }
    #detailPlanModal .modal-body::-webkit-scrollbar-thumb { background: #e11d2e; border-radius: 5px; }
</style>
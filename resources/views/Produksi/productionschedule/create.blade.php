@extends('Produksi.layouts.main')

@section('title', 'Tambah Production Schedule')

@section('content')
<div class="breadcrumb">
    <span>A-Track</span> <span class="separator">></span> 
    <span>Daily Input</span> <span class="separator">></span> 
    <span class="active">Tambah Production Schedule</span>
</div>

{{-- TAMBAHKAN INI: Untuk melihat error validasi atau database --}}
@if ($errors->any())
    <div style="background: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <strong>Gagal Simpan:</strong>
        <ul style="margin-top: 5px; font-size: 13px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div style="background: #fffbeb; border: 1px solid #f59e0b; color: #92400e; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <strong>Error Database:</strong> {{ session('error') }}
    </div>
@endif

<form action="{{ route('productionschedule.store') }}" method="POST" id="formSchedule">
    @csrf

    {{-- 1. INFORMASI UTAMA JOB (HEADER) --}}
    <div class="content-card mb-4">
        <div class="card-header">
            <h5 class="page-title" style="font-size: 15px;">1. Informasi Utama Job</h5>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="form-group">
                    <label>Production Line</label>
                    <select name="IdProductionLine" class="form-select" style="border-radius: 8px;" required>
                        <option value="">Pilih Line - Shift</option>
                        @foreach($lines as $l)
                            <option value="{{ $l->IdProductionLine }}" {{ old('IdProductionLine') == $l->IdProductionLine ? 'selected' : '' }}>
                                {{ $l->NamaProductionLine }} - {{ $l->Shift }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>PIC</label>
                    <select name="NamaPIC" class="form-select" style="border-radius: 8px;" required>
                        <option value="">Pilih PIC</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->NamaKaryawan }}" {{ old('NamaPIC') == $k->NamaKaryawan ? 'selected' : '' }}>
                                {{ $k->NamaKaryawan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                                <div class="form-group">
                    <label>Tanggal Produksi</label>
                    <input type="date" 
                        name="TanggalProduksi" 
                        class="form-control" 
                        style="border-radius: 8px;" 
                        value="{{ old('TanggalProduksi', date('Y-m-d')) }}" 
                        min="{{ date('Y-m-d') }}" {{-- Tambahkan ini --}}
                        required>
                </div>
            </div>
        </div>
    </div>

    {{-- CONTAINER UNTUK DETAIL JOB --}}
    <div id="detail-container">
        @include('Produksi.productionschedule.partials.detail_row', ['index' => 0])
    </div>

    {{-- TOMBOL SIMPAN / BATAL --}}
    <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 30px; align-items: center;">
        {{-- Tombol Simpan --}}
        <button type="button" class="btn btn-primary" 
            style="width: 160px; height: 45px; background-color: #4361ee; border: none; border-radius: 10px; color: white; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.3s ease;" 
            onclick="confirmSave()">
            Simpan
        </button>
        
        {{-- Tombol Batal (Dibuat identik tingginya) --}}
        <a href="{{ route('productionschedule.index') }}" 
            style="width: 160px; height: 45px; background-color: #ffffff; color: #333; border: 1px solid #ddd; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 15px; display: flex; align-items: center; justify-content: center; box-sizing: border-box; transition: all 0.3s ease;">
            Batal
        </a>
    </div>
</div>
</form>

<script>
    let detailIndex = 1;

    // 1. Fungsi Tambah Baris
    function addDetailRow() {
        fetch("{{ url('productionschedule/get-detail-row') }}/" + detailIndex)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(html => {
                document.getElementById('detail-container').insertAdjacentHTML('beforeend', html);
                detailIndex++;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat baris baru.');
            });
    }

    // 2. Fungsi Hapus Baris dengan SweetAlert2
    function removeDetailRow(button) {
        const totalRows = document.querySelectorAll('.detail-item-card').length;

        // Cek Minimal 1 Baris (Pakai SweetAlert biar seragam)
        if (totalRows <= 1) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Hapus',
                text: 'Minimal harus ada 1 detail job.',
                confirmButtonColor: '#3085d6',
            });
            return;
        }

        // Konfirmasi Hapus Baris
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data detail job ini akan dihapus dari form!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const row = button.closest('.detail-item-card');
                row.remove();
                
                // Opsional: Update nomor urut judul biar rapi lagi
                document.querySelectorAll('.detail-item-card').forEach((card, i) => {
                    const title = card.querySelector('.page-title');
                    if (title) title.innerText = `2. Detail Job ${i + 1}`;
                });

                Swal.fire({
                    title: 'Terhapus!',
                    text: 'Detail job telah dilepas.',
                    icon: 'success',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        });
    }

   // 3. Fungsi Simpan Utama
    function confirmSave() {
    const line = document.querySelector('select[name="IdProductionLine"]').value;
    const pic = document.querySelector('select[name="NamaPIC"]').value;
    const inputTanggal = document.querySelector('input[name="TanggalProduksi"]').value;
    
    // --- VALIDASI ITEM DUPLIKAT ---
    const selectedItems = [];
    const itemSelects = document.querySelectorAll('select[name^="details"][name$="[IdItemProduksi]"]');
    let isDuplicate = false;

    itemSelects.forEach(select => {
        if (select.value) {
            if (selectedItems.includes(select.value)) {
                isDuplicate = true;
            }
            selectedItems.push(select.value);
        }
    });

    if (isDuplicate) {
        Swal.fire({
            icon: 'error',
            title: 'Item Duplikat',
            text: 'Terdapat Item Produksi yang sama dalam satu jadwal. Harap periksa kembali!',
            confirmButtonColor: '#3085d6',
        });
        return;
    }
    // ------------------------------

    const hariIni = new Date().toISOString().split('T')[0];

    if(!line || !pic) {
        Swal.fire('Perhatian', 'Harap isi Production Line dan PIC terlebih dahulu', 'warning');
        return;
    }

    if (inputTanggal < hariIni) {
        Swal.fire({
            icon: 'error',
            title: 'Tanggal Tidak Valid',
            text: 'Tanggal produksi tidak boleh kurang dari hari ini!',
            confirmButtonColor: '#3085d6',
        });
        return;
    }

    Swal.fire({
        title: 'Simpan Jadwal Produksi?',
        text: "Pastikan semua item dan Qty sudah benar.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4361ee',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formSchedule').submit();
        }
    });
}

    function calculateWorkTime(element) {
    const container = element.closest('.detail-item-card');
    
    // Ambil value Start dan Finish
    let startTime = container.querySelector('.time-start').value;
    let finishTime = container.querySelector('.time-finish').value;
    const workTimeInput = container.querySelector('.work-time');

    if (startTime && finishTime) {
        // Ambil Jam dan Menit saja (antisipasi jika ada detik dari DB)
        const startParts = startTime.split(':');
        const finishParts = finishTime.split(':');

        // Pastikan konversi ke integer
        const startMinutes = (parseInt(startParts[0]) * 60) + parseInt(startParts[1]);
        const finishMinutes = (parseInt(finishParts[0]) * 60) + parseInt(finishParts[1]);

        let diff = finishMinutes - startMinutes;

        // Logika Shift Malam (jika finish melewati jam 00:00)
        if (diff < 0) {
            diff += 1440; // 24 jam x 60 menit
        }

        // Tampilkan hasilnya langsung ke input Work Time
        workTimeInput.value = diff;
        
        console.log(`Menghitung: ${finishMinutes} - ${startMinutes} = ${diff} Menit`);
    }
}
</script>
@endsection
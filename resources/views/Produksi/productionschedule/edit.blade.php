@extends('Produksi.layouts.main')

@section('title', 'Edit Production Schedule')

@section('content')
<div class="breadcrumb">
    <span>A-Track</span> <span class="separator">></span> 
    <span class="active">Edit Schedule: {{ $schedule->IdPlanSchedule }}</span>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
    </div>
@endif

<form action="{{ route('productionschedule.update', $schedule->IdPlanSchedule) }}" method="POST" id="formSchedule">
    @csrf
    @method('PUT')

    <div class="content-card mb-4">
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="form-group">
                    <label>Production Line</label>
                    <select name="IdProductionLine" class="form-select" required>
                        @foreach($lines as $l)
                            <option value="{{ $l->IdProductionLine }}" {{ $schedule->IdProductionLine == $l->IdProductionLine ? 'selected' : '' }}>
                                {{ $l->NamaProductionLine }} - {{ $l->Shift }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>PIC</label>
                    <select name="NamaPIC" class="form-select" required>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->NamaKaryawan }}" {{ $schedule->NamaPIC == $k->NamaKaryawan ? 'selected' : '' }}>
                                {{ $k->NamaKaryawan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Produksi</label>
                    <input type="date" name="TanggalProduksi" class="form-control" value="{{ date('Y-m-d', strtotime($schedule->TanggalProduksi)) }}" required>
                </div>
            </div>
        </div>
    </div>

    <div id="detail-container">
        @foreach($schedule->details as $index => $det)
            @include('Produksi.productionschedule.partials.detail_row', [
                'index' => $index,
                'detail' => $det // Kirim data detail lama ke partial
            ])
        @endforeach
    </div>

    <div class="form-actions" style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 20px;">
        <button type="button" class="btn btn-primary" onclick="confirmSave()">Update Jadwal</button>
        <a href="{{ route('productionschedule.index') }}" class="btn btn-outline" style="background: #ff5722; color: white; border: none;">Batal</a>
    </div>
</form>

<script>
function confirmUpdate() {
    Swal.fire({
        title: 'Update Data Customer?',
        text: "Apakah Anda yakin ingin menyimpan perubahan?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Perbarui!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEditCustomer').submit();
        }
    });

    // Tambahkan variabel index awal untuk fungsi tambah baris baru saat edit
    let detailIndex = {{ count($schedule->details) }};

    function addDetailRow() {
        fetch("{{ url('productionschedule/get-detail-row') }}/" + detailIndex)
            .then(response => response.text())
            .then(html => {
                document.getElementById('detail-container').insertAdjacentHTML('beforeend', html);
                detailIndex++;
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
// import './bootstrap'; // Sudah di-comment sebelumnya
import Alpine from 'alpinejs';
import jQuery from 'jquery';
import DataTable from 'datatables.net-dt';

// Menambahkan CSS DataTables agar tampilan tabel rapi
import 'datatables.net-dt/css/dataTables.dataTables.min.css';

// Mendefinisikan jQuery agar bisa diakses oleh DataTables dan script di Blade
window.$ = window.jQuery = jQuery;
window.Alpine = Alpine;

// Inisialisasi Alpine
Alpine.start();

// Importar jQuery e Bootstrap
import 'bootstrap';
import 'jquery';

// Importar outros componentes
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import 'bootstrap-datepicker';
import 'bootstrap-timepicker';
import 'select2';
import 'sweetalert2';
import 'chart.js';

// Importar CSS
import '../css/app.css';

// Código JavaScript principal
console.log('Odonto360 - Sistema de Agendamento Odontológico');

// Inicializar componentes quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTables
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.datatable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
            }
        });
    }

    // Inicializar DatePicker
    if (typeof $.fn.datepicker !== 'undefined') {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            language: 'pt-BR',
            autoclose: true
        });
    }

    // Inicializar TimePicker
    if (typeof $.fn.timepicker !== 'undefined') {
        $('.timepicker').timepicker({
            showMeridian: false,
            minuteStep: 15
        });
    }

    // Inicializar Select2
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            language: 'pt-BR'
        });
    }
});

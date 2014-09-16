

var picker = new Pikaday(
    {
        field: document.getElementById('event_date_input'),
        i18n: {
            previousMonth : 'Mes Anterior',
            nextMonth     : 'Mes Seguinte',
            months        : ['Xaneiro','Febreiro','Marzo','Abril','Maio','Xuño','Xullo','Agosto','Setempro','Outubro','Novembro','Decembro'],
            weekdays      : ['Domingo','Luns','Martes','Mercores','Xoves','Venres','Sabado'],
            weekdaysShort : ['Dom','Lun','Mar','Mer','Xov','Ven','Sav']
        },
        firstDay: 1
    });
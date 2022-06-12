$(function (){


    var count = 1200; // ustawienie licznika na 60 sekund
    var counter = setInterval(timer, 1000); // ustawienie funkcji odpowiedajacej za cykliczne wywolanie(co 1 s) funkcji timer()

    function timer()
    {
        --count;
        var minutes = Math.floor(count / 60); // obliczanie ile minut zostało
        var sec = count % 60; //obliczanie ile sekund zostało reszta z dzielenia licznika przez 60 sekund

        if(sec<10) sec = '0' + sec; // jeżeli mniej niż 10 sekund to wyświetl sekundy w formacie 00 zamiast 0  
        var out = minutes + ':' + sec; //tekst wyswietlony uzytkownikowi
        $("#timer").html(out); // przypisanie tekstu timera do odpowiedniego elementu html
        if( count <= 0) //licznik osiągnął 0 
        {
            //licznik się wyzerował należy podjąć odpowiednią akcje
            location.replace('/logout');

            clearInterval(counter); //zatrzymanie licznika
            return; 
        }
    }

    $('[data-toggle="tooltip"]').tooltip();


    // wybranie usługi po kliknięciu na nią
    $("#order_service").on('change',(function(e){
        e.preventDefault();
        var value = $(this).val();

        //location.replace('/user/order/'+value+'/MINI/0/0');
    }));

    // kliknięcie i odpalenie kalkulatora
    $(".form-widget").on('change',(function(e){
        e.preventDefault();

        order();
    }));

    // włączenie kalkulatora
    order();

    // kalkulator
    function order() {
        var order_service = $("#order_service").val();
        var order_pack = $("#order_pack").val();
        var order_time = $("#order_time").val();

            switch(order_pack) {
                case 'MINI':
                    var amount = 12;
                break;
                case 'MEDIUM':
                    var amount = 16;
                break;
                case 'MAXI':
                    var amount = 20;
                break;
                case 'PRO':
                    var amount = 25;
                break;
            }

            if(order_time == 1) {
                amount = amount + 1;
            }

            var data = (amount*23)/123;
            var amount_bez = amount - data;
            var suma = amount_bez*order_time;
            var vat = (suma*23)/100;
            var sumvat = suma + vat;

        $("#order_table_name").text(order_service+' '+order_pack);
        $("#order_table_cost").text(amount_bez.toFixed(2)+' zł/msc netto');
        $("#order_table_month").text(order_time);
        $("#order_table_sum").text(suma.toFixed(2)+' zł');
        $("#order_table_vat").text(vat.toFixed(2)+' zł');
        $("#order_table_sumvat").text(sumvat.toFixed(2)+' zł');  
    }

});
<!--
    '%id'=>,
    '%ifelse'=>,
    '%tablevalues'=>,    
!-->

<script>
    jQuery(document).ready(function ($) {
        $("#mwpc-plus-%id").click(function() {
            if (typeof mwpc_counter_%id == 'undefined') {
                mwpc_counter_%id = 0;
            }
            mwpc_counter_%id++;
            let value = $("#mwpc-type-%id").children("option:selected").val();
            %ifelse
            $("#mwpc-cancel-%id").click(function() {
                $('#mwpc-item-%id-' + this.getAttribute("itemid")).remove();
            });
            $("#mwpc-save-%id").click(function(){
                let validated = true;
                let objects = $('tbody#mwpc-detail-body-%id tr td input');
                for (i=0; i<objects.length;++i) {
                    if (objects[i].required && objects[i].value.length == 0) {
                        objects[i].setAttribute('placeholder', 'Preencha este campo');
                        objects[i].focus();
                        validated = false;
                        break;
                    }
                }
                if (validated) {
                    let itemid = this.getAttribute("itemid");
                    let row = "<tr>";
                    key = "";
                    for (i=0; i<objects.length;++i) {
                        key += objects[i].id + ':' + objects[i].value;
                        if (i < objects.length - 1) key += ";";
                    }
                    row += '<td></td><td><input type="hidden" name="%id[]" value="' + key + '"></td>';
                    for (i=0; i<objects.length;++i) {                            
                        row += '<td>' + objects[i].value + '</td>';
                    }
                    row += "</tr>";
                    console.log(row);
                    $("#mwpc-table-%id tr:last").after(row);
                    $('#mwpc-item-%id-' + this.getAttribute("itemid")).remove();
                }
            });             
        });
    });
</script>

<div class="mwpc-combobox-%id" style="float: inline">
    <select id="mwpc-type-%id">
        %options
    </select>      
    <div id="mwpc-plus-%id" class="button action">+</div>
</div>
<div id="mwpc-canvas-%id">
</div>
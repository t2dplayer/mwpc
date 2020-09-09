<style>
#table-mwpc-wrapper {
  position:relative;
}
#table-scroll {
  height:150px;
  overflow:auto;  
  margin-top:20px;
}
#table-mwpc-wrapper table {
  width:100%;
    
}
  
</style>
<!--
<p style="padding:10px;">
    <label>%searchlabel</label>
    <input class="regular-text" id="pgquim-filter" type="text" placeholder="%searchplaceholder">
</p>
-->
<div id="table-mwpc-wrapper">
  <div id="table-scroll">
    <table class="wp-list-table fixed striped">
        <thead>
            <tr>
                <th class="manage-column check-column" scope="col"></th>
                %columns
            </tr>
        </thead>
        <tbody id="pgquim-table">        
            %rows        
        </tbody>
    </table>
  </div>
</div>
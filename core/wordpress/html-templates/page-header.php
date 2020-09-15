<!--
$data  = [
    '%title'=>'',
    '%link'=>'',
    '%addnew'=>'',
    '%message'=>'',
    '%id'=>'',
    '%requestpage'=>'', 
];
-->
<style>
    a#mwpc-href {
        font-weight:normal;
        color:#555;
        text-decoration:none;
    } 
    a#mwpc-href:hover {
        text-decoration:underline;
        font-weight:bold;
    }
</style>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit">
        <br />
    </div>
  <h2>%title
  <a class="add-new-h2" href="%link">%addnew</a>
  </h2>
  %message
  <form id="%id-table" method="GET">
      <input type="hidden" name="page" value="%requestpage"/>
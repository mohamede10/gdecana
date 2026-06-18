<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Fiche Notes Algebra</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@12.1.0/dist/handsontable.min.css">
<script src="https://cdn.jsdelivr.net/npm/handsontable@12.1.0/dist/handsontable.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>#excelContainer{width:100%;height:700px;margin-top:20px;}</style>
</head>
<body>

<h2>📊 Fiche de Notes - MATHEMATIQUE (ALGEBRE)</h2>
<input type="file" id="fileInput">
<button id="saveBtn">💾 Enregistrer les modifications</button>

<div id="excelContainer"></div>

<script>
let hot;

$('#fileInput').on('change', function(){
    const file = this.files[0];
    if(!file) return;
    const formData = new FormData();
    formData.append('fichier_excel', file);
    
    $.ajax({
        url:'load_excel.php',
        type:'POST',
        data: formData,
        contentType:false,
        processData:false,
        success: function(res){
            const data = JSON.parse(res);
            hot = new Handsontable(document.getElementById('excelContainer'),{
                data:data,
                rowHeaders:true,
                colHeaders:true,
                contextMenu:true,
                filters:true,
                dropdownMenu:true,
                licenseKey: 'non-commercial-and-evaluation'
            });
        }
    });
});

$('#saveBtn').on('click', function(){
    if(!hot) return;
    const data = hot.getData();
    const keys = hot.getColHeader().map((_,i)=>String.fromCharCode(65+i));
    const jsonData = data.map(row=>{
        let obj={};
        row.forEach((val,i)=>obj[keys[i]]=val);
        return obj;
    });

    $.ajax({
        url:'save_notes.php',
        type:'POST',
        data: JSON.stringify(jsonData),
        contentType: 'application/json',
        success:function(res){
            alert('✅ Modifications enregistrées !');
        }
    });
});
</script>

</body>
</html>

<!doctype html>
<html>
<head><meta charset="utf-8"><title>Video Upload Demo</title></head>
<body>
<h3>Upload a video (client-side compression demo placeholder)</h3>
<input type="file" id="file" accept="video/*"><button id="upload">Upload</button>
<script>
document.getElementById('upload').addEventListener('click', async ()=>{
  const f=document.getElementById('file').files[0]; if(!f) return alert('select file');
  const fd=new FormData(); fd.append('file', f);
  const token = prompt('Paste your API token');
  const res = await fetch('/api/videos/upload',{method:'POST',headers:{'Authorization':'Bearer '+token},body:fd});
  alert(await res.text());
});
</script>
</body>
</html>

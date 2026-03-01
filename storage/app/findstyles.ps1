$word = New-Object -ComObject Word.Application
$word.Visible = $false
$doc = $word.Documents.Add()
$doc.Styles | ForEach-Object { $_.NameLocal } | Where-Object { $_ -match 'list|elenco|bullet|puntat|numero' } | Sort-Object
$doc.Close($false)
$word.Quit()

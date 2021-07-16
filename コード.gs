function doPost(e) {
  const insert_at = 2;//1行目がヘッダーなので2行目に挿入
  const sheet = SpreadsheetApp.getActiveSheet();
  let insert_value;
  try {
    const params = e.parameter;
    insert_value = [[
      params.name,
      params.address,
      params.tel,
      params.email,
      params.amount,
      params.post_at,
      params.ip,
      params.useragent
    ]];
  } catch (error) {
    return return_error('error_a');
  }
  const column_length = insert_value[0].length;
  const lock = LockService.getScriptLock();
  if (lock.tryLock(10000)) {//ロック時間はどの位が最適か分からない
    try {
      sheet.insertRows(insert_at, 1);//ここでは失敗しないと信じてる。
      sheet.getRange(insert_at, 1, 1, column_length).setValues(insert_value);
    } catch (error) {
      sheet.deleteRow(insert_at);//挿入した空行を削除
      return return_error('error_b');
    } finally {
      lock.releaseLock();
    }
  } else {
    return return_error('error_timeout');
  }
  return return_success();
}

function return_success() {
  const return_json = {
    result: 'success'
  };
  return ContentService.createTextOutput(JSON.stringify(return_json)).setMimeType(ContentService.MimeType.JSON);
}

function return_error(_message) {
  const return_json = {
    result: 'error',
    message: _message
  };
  return ContentService.createTextOutput(JSON.stringify(return_json)).setMimeType(ContentService.MimeType.JSON);
}

<?php

function translate($word) {
  switch ($word) {
    case 'index.php':
      $r = 'Особистий кабінет';
      break;
    case 'client.php':
      $r = 'Інформація';
      break;
    case 'bill.php':
      $r = 'Рахунки';
      break;
    case 'pay.php':
      $r = 'Оплати';
      break;
    case 'pass.php':
      $r = 'Змінити пароль';
      break;
    case 'connect.php':
      $r = 'Підключення';
      break;
    case 'limit.php':
      $r = 'Ліміти';
      break;
    case 'indication.php':
      $r = 'Показники';
      break;
    case 'client_edit.php':
      $r = 'Змінити інформацію';
      break;
    case 'feedback.php':
    case 'feedback_view.php':
      $r = 'Зворотній зв’язок';
      break;
    case 'indication_edit.php':
      $r = 'Внести показники';
      break;
    case 'menu':
      $r = 'Меню';
      break;
    case 'exit':
      $r = 'Вихід';
      break;
    case 'personal_code':
    case 'lic':
      $r = 'Особовий рахунок';
      break;
    case 'sub_indication.php':
      $r = 'Показники субабонентів';
      break;
    case 'meter':
    case 'num_meter':
    case 'type_meter':
      $r = 'Лічильник';
      break;
    case 'ground':
    case 'id_area':
      $r = 'Майданчик';
      break;
      case 'val_prev':
          $r = 'Попередні показники';
          break;
    case 'title_count':
      $r = 'Кількість рядків у результаті запиту';
      break;
    case 'p.name_eqp':
      $r = 'Точка обліку';
      break;
    case 'm.num_eqp':
    case 'num_eqp':
      $r = 'Номер лічильника';
      break;
    case 'koef':
      $r = 'Коеф. тр.';
      break;
    case 'koef_title':
      $r = 'Коефіцієнт трансформації';
      break;
    case 'name_zone':
    case 'zone_name':
    case 'id_zone':
      $r = 'Зона';
      break;
    case 'energy':
    case 'kind_energy':
      $r = 'Вид енергії';
      break;
    case 'type':
    case 'type_name':
      $r = 'Тип';
      break;
    case 'carry':
      $r = 'Розрядність';
      break;
    case 'dat_prev':
      $r = 'Дата';
      break;
    case 'value_prev':
      $r = 'Попередні показники';
      break;
    case 'value':
      $r = 'Показники';
      break;
    case 'indication_view.php':
      $r = 'Занесені показники';
      break;
    case 'calc_ind_pr':
      $r = 'Споживання';
      break;
    case 'calc':
      $r = 'Внести показники';
      break;
    case 'instruction':
      $r = 'Інструкція';
      break;
    case 'id_pref':
      $r = 'Вид нарахування';
      break;
    case 'deb_b':
      $r = 'Борг на поч. місяця';
      break;
    case 'kt_b':
      $r = 'Кредит на поч. місяця';
      break;
    case 'summ_avans':
      $r = 'Сума авансу';
      break;
    case 'summ_avans_title':
      $r = 'Сума авансу, яка повинна бути оплачена на поточну дату';
      break;
    case 'summ_bill':
      $r = 'Сума рахунків';
      break;
    case 'kvt_bill':
      $r = 'Споживання, кВт/г';
      break;
    case 'sum_pay':
      $r = 'Оплачено';
      break;
    case 'deb_e':
      $r = 'Борг на кінець місяця';
      break;
    case 'kt_e':
      $r = 'Кредит на кінець місяця';
      break;
    case 'client_point':
      $r = 'Точка обліку';
      break;
    case 'sub_name':
      $r = 'Ім\'я субабонента';
      break;
    case 'sub_point':
      $r = 'Точка обліку субабонента';
      break;
    case 'dat_ind':
      $r = 'Дата показників';
      break;
    case 'dat_prev_ind':
      $r = 'Дата попередніх показників';
      break;
    case 'full_name':
      $r = 'Повне найменування';
      break;
    case 'short_name':
      $r = 'Скорочене найменування';
      break;
    case 'addr':
    case 'addr_reg':
    case 'addr_live':
      $r = 'Адреса';
      break;
    case 'addr_tax':
      $r = 'Юридична адреса';
      break;
    case 'doc_num_date':
      $r = 'Номер і дата договору';
      break;
    case 'dt_doc':
    case 'date_agreem':
      $r = 'Дата договору';
      break;
    case 'okpo_num':
      $r = 'Код ЄДРПОУ';
      break;
    case 'okpo_num_title':
      $r = 'Код єдиного державного реєстру підприємств та організацій України';
      break;
    case 'personal_code':
    case 'login':
      $r = 'Особовий рахунок';
      break;
    case 'code':
          $r = 'Особовий рахунок';
          break;
    case 'eic':
          $r = 'EIC код';
          break;
    case 'period_indicat':
      $r = 'Період розрахунку';
      break;
    case 'dt_indicat':
      $r = 'День звіту';
      break;
    case 'operator.php':
      $r = 'Пароль';
      break;
    case 'bill_reg_num':
      $r = 'Номер рахунку';
      break;
    case 'reg_date':
      $r = 'Дата рахунку';
      break;
    case 'sum':
      $r = 'Сума';
      break;
    case 'nds':
      $r = 'ПДВ';
      break;
    case 'kvt':
      $r = 'кВт/г';
      break;
    case 'mmgg_bill':
      $r = 'За який місяць рахунок';
      break;
      case 'auto_val':
          $r = 'Передано системою АСКОЕ';
          break;
      case 'auto_date':
          $r = 'Дата передачі системою АСКОЕ';
          break;
      case 'mmgg':
          $r = 'Місяць';
          break;
    case 'idk_doc':
      $r = 'Вид документа';
      break;
    case 'pay_reg_num':
      $r = 'Номер оплати';
      break;
    case 'pay_date':
      $r = 'Дата оплати';
      break;
    case 'mmgg_pay':
      $r = 'Період за який сплачують';
      break;
    case 'limit':
      $r = 'Ліміт';
      break;
    case 'month_limit':
      $r = 'Місяць';
      break;
    case 'change_date':
      $r = 'Дата останньої зміни';
      break;
    case 'doc_limit':
      $r = 'Вид ліміту';
      break;
    case 'doc_reg_date':
      $r = 'Дата документу';
      break;
    case 'idk_document':
      $r = 'Тип документу';
      break;
    case 'value_dem':
      $r = 'Споживання за місяць';
      break;
    case 'tarif_name':
    case 'id_gtar':
      $r = 'Тариф';
      break;
    case 'power':
      $r = 'Потужність';
      break;
    case 'term_control':
      $r = 'Період повірки';
      break;
    case 'period':
          $r = 'Період споживання';
          break;
    case 'dt_control':
      $r = 'Дата останньої повірки';
      break;
    case 'newcontrol':
      $r = 'Дата наступної повірки';
      break;
    case 'password':
      $r = 'Пароль';
      break;
    case 'stat.php':
      $r = 'Статистика';
      break;
    case 'ip_addr':
      $r = 'IP-адреса';
      break;
    case 'action':
      $r = 'Дія';
      break;
    case 'now':
      $r = 'Дата додавання';
      break;
    case 'file':
      $r = 'Файл';
      break;
    case 'send':
      $r = 'Відіслати';
      break;
    case 'upload':
    case 'upload.php':
      $r = 'Завантаження';
      break;
    case 'note':
      $r = 'Примітка';
      break;
    case 'download':
    case 'download.php':
      $r = 'Файл рахунку';
      break;
    case 'delete':
      $r = 'Видалити';
      break;
    case 'confirm':
      $r = 'Підтвердити';
      break;
    case 'confirmed':
      $r = 'Підтверджено';
      break;
    case 'pasport':
      $r = 'Паспорт';
      break;
    case 'doc_name':
      $r = 'Вид документу';
      break;
    case 'fio':
      $r = 'ПIБ';
      break;
    case 'who_doc':
      $r = 'Ким виданий';
      break;
    case 'tax_number':
    case 'ident_cod_l':
      $r = 'Ідентифікаційний код';
      break;
    case 'home_phone':
      $r = 'Домашній телефон';
      break;
    case 'work_phone':
      $r = 'Робочий телефон';
      break;
    case 'mob_phone':
      $r = 'Мобільний телефон';
      break;
    case 'idk_house':
      $r = 'Тип будинку';
      break;
    case 'heat_area':
      $r = 'Тип обігрівачів';
      break;
    case 'saldo':
      $r = 'Сальдо';
      break;
    case 'lgt':
      $r = 'Пільга';
      break;
    case 'family_cnt':
      $r = 'Кількість людей';
      break;
    case 'dt_start':
      $r = 'Дата початку';
      break;
    case 'dt_end':
      $r = 'Дата закінчення';
      break;
    case 'name':
      $r = 'Назва';
      break;
    case 'tp':
          $r = 'Трансформаторна підстанція';
          break;
    case 'dt_b':
      $r = 'Дата установки';
      break;
    case 'value_ind':
      $r = 'Попередні показники';
      break;
    case 'value_new':
      $r = 'Поточні показники';
      break;
  case 'value_diff':
      $r = 'Споживання, кВт.';
      break;
      case 'val_new':
          $r = 'Останні введені в кабінеті показники';
          break;

      case 'date_new':
          $r = 'Поточні показники [дата]';
          break;
    case 'debet_e':
      $r = 'Борг';
      break;
  case 'zone':
      $r = 'Зона';
      break;
    case 'avans_val':
      $r = 'Переплата';
      break;
    case 'id_doc':
      $r = 'Рахунок';
      break;
    case 'news':
      $r = 'Новини';
      break;
    case 'email':
      $r = 'Електронна пошта';
      break;
    case 'post':
      $r = 'Зберегти';
      break;
    case 'cancel':
      $r = 'Скасувати';
      break;
    case 'last_reg_date':
      $r = 'Дата останнього платежу';
      break;
    case 'pay_reg_date':
      $r = 'Дата оплати';
      break;
    case 'pay_sum':
      $r = 'Сума оплати';
      break;
    case 'sum_add':
      $r = 'Нарахована сума, грн.';
      break;
    case 'p_all':
    case 'u_all':
      $r = 'Відвідування: Зареєстровано';
      break;
    case 'p_unique':
      $r = 'Унікальних';
      break;
    case 'p_indication':
    case 'u_indication':
      $r = 'Введені показники';
      break;
    case 'indic_type':
      $r = 'Походження';
      break;
    case 'demand':
      $r = 'Спожито, кВт.год.';
      break;
    case 'demand_all':
          $r = 'Спожито всього, кВт.год.';
          break;
    case 'demand0':
          $r = 'Спожито без зони, кВт.год.';
          break;
    case 'demand10':
          $r = 'Спожито день, кВт.год.';
          break;
    case 'demand9':
          $r = 'Спожито ніч, кВт.год.';
          break;
    case 'demand8':
          $r = 'Спожито 3-зони пік, кВт.год.';
          break;
    case 'demand7':
          $r = 'Спожито 3-зони полупік, кВт.год.';
          break;
    case 'demand6':
          $r = 'Спожито 3-зони ніч, кВт.год.';
          break;
      case 'last_pay_sum':
      $r = 'Сума останнього платежу';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    case '':
      $r = '';
      break;
    
    default:
      $r = $word;
      break;
  }
  return $r;
}

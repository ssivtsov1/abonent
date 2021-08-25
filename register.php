<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Реєстрація</title>
    <?php
    require './func.php';
    require './config.php';
    require './head.php';
    get_head('date');

    if($_GET['error']==1):

    ?>
      <div class="col-md-8">
          <h4> <span class="label label-danger"><? echo("Увага! неправильно введений № лічильника. Зверніться за довідкою за телефоном ".$_SESSION['register']['tel']); ?></span></h4>
          <br>
          <h4> <span class="label label-danger"><? echo("Дзвінки приймаються з 8 до 17 години."); ?></span></h4>
          <p><a href="http://192.168.55.1/cekservice" target="_blank" class="btn btn-default" style="margin-left: 10px">Перевірка належності до мережі ЦЕК</a></p>
      </div>
     <? endif;
    if($_GET['error']==3):

        ?>
        <div class="col-md-8">
            <h4> <span class="label label-danger"><? echo("Увага! Введіть тип лічильника."); ?></span></h4>
            <br>
        </div>
    <? endif;

    if($_GET['error']==10):

        ?>
        <div class="col-md-8">
            <h4> <span class="label label-danger"><? echo("Увага! Введіть особовий рахунок. Зверніться за довідкою за телефоном ".$_SESSION['register']['tel']); ?></span></h4>
            <br>
        </div>
    <? endif;

    switch ($_GET['error']) {
      case '1':
        $errColor[1] = 'has-error';
        break;
      case '2':
        $errColor[2] = 'has-error';
        $flag=1;
        break;
      case 'db':
        $errColor['db'] = 'has-error';
        break;
      
    }
    ?>
    <style>
      input[type=text],input[type=email],input[type=password] {
        width: 180px;
      }
    </style>
    <script type="text/javascript">
        window.onload=function() {

            $("input[type=password]").val('');
        }

      function checkboxFoo() {
        var x = document.getElementById("checkboxId").checked;
        if (x === true) {
          document.getElementById('submitId').disabled = false;
        } else {
          document.getElementById('submitId').disabled = true;
        }
      }
    </script>
  </head>
  <body style="cursor: default">

    <div class="col-lg-offset-1 col-md-offset-1 col-sm-offset-0 col-xs-offset-0" style="min-width: 550px">
      <form method="POST" action="a.php?action=3" class="form-horizontal" style="margin: 15px 45px">

        <span class="label label-danger l_clear" >Увага! Виберіть правильно дільницю, яка відповідає Вашому регіону.</span>

        <div class="form-group <?= $errColor['db'] ?>" style="width: 360px; margin-top: 10px">
          <label class="label label-default"><?= translate('Дільниця') ?></label>
          <?php echo getOptions('db', '1|Апостоловські РЕМ,2|Гвардійські РЕМ,3|Інгулецькі РЕМ,4|Павлоградські РЕМ,
            5|Вільногірські РЕМ,6|Жовтоводські РЕМ,7|Криворізькі РЕМ,8|Дніпровські РЕМ',$_SESSION['db']) ?>
        </div>

<!--        <div class="form-group">-->
<!--          <label class="label label-default">--><?//= translate('lic') ?><!--</label>-->
<!--          <input class="form-control input-sm" type="text" name="lic" value="--><?//= $_SESSION['register']['lic'] ?><!--" maxlength="32" placeholder="?????????" autocomplete="off" required>-->
<!--        </div>-->

        <div class="form-group">
          <label class="label label-default"><?= translate('password') ?></label>
          <input class="form-control input-sm" type="password" name="pass" value="" maxlength="32" autocomplete="off" required>
        </div>
        <div class="form-group">
          <label class="label label-default"><?= translate('email') ?></label>
          <input class="form-control input-sm" type="email" name="email" value="<?= $_SESSION['register']['email'] ?>" maxlength="100" autocomplete="off">
        </div>

<!--        <div class="form-group <?= $errColor[1] ?>">
          
          <label class="label label-default"><?= translate('last_reg_date') ?></label>
          <input class="form-control input-sm date" type="text" name="date" value="<?= $_SESSION['register']['date'] ?>" placeholder="??.??.2016" autocomplete="off" required>
        </div>-->
        
        <div class="form-group <?= $errColor[1] ?>">
          <div style="margin: 8px 0 2px 0; font-size: 160%"><label class="label label-info">Інформація для забезпечення безпеки</label></div>
<!--            <div>-->
<!--                Увага! Якщо споживач має декілька договорів, потрібно вводити № лічильника.-->
<!--            </div>-->
          <!--  translate('last_pay_sum') -->
          <!-- $_SESSION['register']['sum'] -->
          <!-- name="sum" -->
<!--        Мобільний телефон або № лічильника    -->
            <?php
            if($_GET['error']!=10):?>
                <label class="label label-danger"><?= translate('№ лічильника') ?></label>
                <input class="form-control input-sm" type="text" name="phone" value="<?= $_SESSION['register']['mob_phone'] ?>" placeholder="??????????" autocomplete="off" required>
            <? endif; ?>
            <?php
            if($_GET['error']==3):?>
             <br>
             <label class="label label-danger"><?= translate('Тип лічильника') ?></label>
            <?php
            $k=$_SESSION['register1']['kol_cnt'];
            $s='';
            for($j=0;$j<$k;$j++){
                $s.= ($j+1).'|'.$_SESSION['register1']['type_cnt'][$j].',';
            }
            $y=mb_strlen($s,'UTF-8');
            $s=mb_substr($s,0,$y-1);
            ?>
            <div class="form-group" style="width: 170px; margin-left: 0px">
            <?php
            echo getOptions('type_cnt',$s);
             $_SESSION['register1']['check']=1;
            ?>
            </div>
            <? endif; ?>

            <?php
            if($_GET['error']==10):?>
                <br>
                <label class="label label-danger"><?= translate('Особовий рахунок') ?></label>
                <input class="form-control input-sm" type="text" name="lic_cnt" value="<?= $_SESSION['register']['lic_cnt'] ?>" placeholder="??????????" autocomplete="off" required>
                <?php $_SESSION['register1']['check']=2; ?>
            <? endif; ?>

        </div>
        <div class="form-group <?= $errColor[2] ?>">
          <img class="pull-left" src="captcha.php" style="border: 1px solid #ccc; border-radius: 3px;" alt="Захисний код">
          <input class="form-control input-sm pull-left" style="width: 100px; margin-top: 26px; margin-left: 10px;" placeholder="???" type="text" maxlength="3" name="captcha" value="" autocomplete="off" required>
        </div>

        <div class="form-group">
          <div style="font-size: 70%; margin: 0; width: 550px">Керуючись статтею 12 Закону України від 01 червня 2010 № 2297-VI «Про захист персональних
            даних» (далі Закон № 2297), повідомляємо, що надані Вами при укладанні договору персональні дані: ідентифікаційні дані, паспортні дані, 
            реєстраційний номер облікової картки платника податків, дані стосовно права власності або користування об'єктом нерухомості, інші відомості, 
            що відповідають вимогам чинного законодавства, включені до бази персональних даних «База даних контрагентів ПрАТ «ЦЕНТРАЛЬНА ЕНЕРГЕТИЧНА КОМПАНІЯ».
            <br>Ваші права, як суб'єкта персональних даних, передбачені в статті 8 Закону України № 2297-VI. ПрАТ «ЦЕНТРАЛЬНА ЕНЕРГЕТИЧНА КОМПАНІЯ» зберігає персональні
            дані в картотеці договорів та в автоматизованій системі. База персональних даних розміщена за адресою: м. Дніпро, вул. Кедріна, 28 та за
            місцем розташування відокремлених підрозділів.
            <br>Використання персональних даних проводиться з метою забезпечення обліку наданих послуг згідно укладеного договору, податкових відносин
            та відносин у сфері бухгалтерського обліку, інших відносин, передбачених чинним законодавством України.
            <br>Ваші персональні дані можуть надаватись державним органам, установам, організаціям та іншим особам тільки у випадках та порядку,
            передбачених чинним законодавством України.
          </div>
          <div style="font-size: 90%; font-weight: bold; width: 550px"><input type="checkbox" id="checkboxId" onchange="checkboxFoo()" class="checkbox-inline" checked> Даю згоду на обробку, зберігання та використання моїх персональних даних в базі  даних контрагентів ПрАТ «ЦЕНТРАЛЬНА ЕНЕРГЕТИЧНА КОМПАНІЯ».</div>
        </div>
        <div class="form-group" style="margin-top: 15px">
          <a href="./index.php" class="btn btn-danger"><?= translate('cancel') ?></a>
          <button type="submit" class="btn btn-success" id="submitId" style="margin: 0 20px"><?= translate('post') ?></button>
        </div>

      </form>
    </div>
    <?= $_POST['db'] ?>

  </body>
</html>

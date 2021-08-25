<?php ob_start() ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <?php
    #ini_set('display_errors',1);
    #ini_set('error_reporting',2047);   

    require './func.php';
    require './config.php';
    require './head.php';
    echo '<title>' . translate(basename(__FILE__)) . '</title>';
    get_head(1);
    ?>
  </head>
  <body style="box-sizing: border-box" class="curd">
    <div style="position: fixed; top: 4px; left: 4px;">
      <a href="http://cek.dp.ua/"><img src="Logo.png"></a>
    </div>
    <?php
//    $let_time = substr(date("H:i:s"),0,2);
//    if ($let_time=='04'):
//    ?>
<!--    <div class="jumbotron" style="height: 265px;">-->
<!--        <div class="text-center">-->
<!--            <h1>Особистий кабінет</h1>-->
<!--            <p>фізичної особи</p>-->
<!--            <div class="alert alert-danger">Увага! Зараз проходе актуалізація данних, сервіс буде доступним після 5 години ранку.</div-->
<!--        </div>-->
<!--    </div>-->
<!--    --><?php
//    exit;
//    endif;
    if (filter_input(INPUT_COOKIE, 'abon_cabinet_id')) {
      $name = filter_input(INPUT_COOKIE, 'abon_cabinet_id');
    }
    if ($_SESSION['register']['lic']) {
      $name = $_SESSION['register']['lic'];
    }

    unset($_SESSION['register']); // clear register
    unset($_SESSION['register1']);

    if ($_GET['id'] === '0') {
      $warning_str = '<div class="alert alert-warning"><b>Помилка!</b> Комбінація логіна та паролю - не вірна!<br>Допускається повторна реєстрація.</div>';
    }
    $ok=0;
    if ($_GET['reg'] == 'ok') {
      $ok=1;
      $warning_str = '<div class="alert alert-success">
        
        <b>Успішне завершення реєстрації</b>. Ваш особовий рахунок '.$name.
      '. Введіть свій особовий рахунок, пароль і натисніть кнопку входу.</div>';
    }
    ?>

    <form method="POST" action="a.php?action=index">
      <div class="jumbotron" style="height: 265px;">
        <div class="text-center">
          <h1>Особистий кабінет</h1>
          <p>побутового користувача електроенергією</p>
        </div>
        <div class="<?= $col_model_f ?>">
          <div class="<?= $col_model ?>">
            <label class="label label-default"><?= translate('personal_code') ?></label>
            <input class="form-control" type="text" name="name" value="<?= $name ?>" autocomplete="off" required>
          </div>
          <div class="<?= $col_model ?>">
            <label class="label label-default"><?= translate('password') ?></label>
            <input class="form-control" type="password" name="secret" value="" autocomplete="off" required>
          </div>
          <div class="<?= $col_model ?>" style="width: 360px">
            <label class="label label-default"><?= translate('Дільниця') ?></label>
            <?php echo getOptions('db', '1|Апостоловські РЕМ,2|Гвардійські РЕМ,3|Інгулецькі РЕМ,4|Павлоградські РЕМ,
            5|Вільногірські РЕМ,6|Жовтоводські РЕМ,7|Криворізькі РЕМ,8|Дніпровські РЕМ',$_SESSION['db']) ?>
          </div>
          <div class="<?= $col_model_d ?>" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary" title=""><span class="glyphicon glyphicon-log-in"></span> Вхід</button>


          </div>




        </div>
      </div>

        <?php if ($ok==0):?>

        <div class="reg_cab">
            <span class="reg_lab">Якщо зайшли вперше спочатку потрібно зареєструватись:</span>

            <a href="./register.php" class="btn btn-warning" style="margin: 0 15px">Реєстрація</a>
        </div>

        <div class="reg_cab">
            <div class="reg_lab1">Якщо вже зареєструвались, але забули особовий рахунок:</div>
            <!--                <button type="submit" class="btn btn-primary" title=""><span class="glyphicon glyphicon-log-in"></span> Вхід</button>-->
            <a href="./remind_lic.php" class="btn btn-warning" style="margin: 0 0px">Дізнатися особовий рахунок?</a>
        </div>
         <?php endif; ?>

    </form>
    <div class="reg_cab_last">
<!--    <button type="submit" class="btn btn-primary" title=""><span class="glyphicon glyphicon-book"></span> Інструкція користувача</button>-->
        <a href="./instruction1.pdf" target="_blank" class="btn btn-default">Інструкція користувача</a>
        <a href="http://cek.dp.ua/cekservice" target="_blank" class="btn btn-default" style="margin-left: 10px">Перевірка належності до мережі ЦЕК</a>
        <a href="./forget_pass.php" target="_blank" class="btn btn-default" style="margin-left: 10px">Забули пароль?</a>
    </div>
<!--    <p><a href="http://cek.dp.ua/cekservice" target="_blank" class="btn btn-default">Перевірте належність вашої адреси до мережі ЦЕК</a></p>-->

    <br>
    <?= $warning_str ?>
    <?php
    //echo $_SESSION['db'];
    $vid = $_GET['id'];
    $lic_sch = $_GET['lic_sch'];
    if (empty($lic_sch))
        $lic_sch=' не визначено, можливо неправильно введено адресу.';
    if($vid==10):?>
    <div class="col-md-8">
        <h4> <span class="label label-danger"><? echo('Ваш особовий рахунок '. $lic_sch); ?></span></h4>
    </div>
    <?php endif; ?>

    <?php
    $vid = $_GET['id'];
    $passwd = $_GET['passwd'];

    if (empty($passwd))
        $p='Ваш пароль невизначено [неправильно введено дані]';
    else {

        //$p = 'Ваш пароль відправлено на електронну пошту';
        $p = "Ваш пароль: $passwd. Лист з паролем відправлено також на електронну скриньку.";
    }

    if($vid==11):?>
    <div class="col-md-8">
        <h4> <span class="label label-danger"><? echo($p); ?></span></h4>
    </div>
    <?php endif; ?>
    <?php
//    if(($vid==11) && ($passwd=='0')):?>
<!--    <div class="col-md-8">-->
<!--        <h4> <span class="label label-danger">--><?// echo($p); ?><!--</span></h4>-->
<!--    </div>-->
<!--    --><?php //endif; ?>

    <div class="clearfix"></div>

    <footer class="footer">

        <div id="container_footer" class="container">
            <p class="pull-left">&copy; ЦЕК <?= date('Y') ?> &nbsp &nbsp
<!--                --><?//= Html::a('Головна',["index"],['class' => 'a_main']); ?><!-- &nbsp &nbsp-->
<!--                --><?//= Html::a("<a class='a_main' href='http://cek.dp.ua'>сайт ПрАТ ПЕЕМ ЦЕК</a>"); ?>
            </p>
            <p class="pull-right">
                <img class='footer_img' src="./Logo.png">
            </p>
            <?php
            $day = date('j');
            $month = date('n');
            $day_week = date('w');
            switch ($day_week)  {
                case 0:
                    $dw = 'нед.';
                    break;
                case 1:
                    $dw = 'пон.';
                    break;
                case 2:
                    $dw = 'вівт.';
                    break;
                case 3:
                    $dw = 'середа';
                    break;
                case 4:
                    $dw = 'четв.';
                    break;
                case 5:
                    $dw = 'п’ятн.';
                    break;
                case 6:
                    $dw = 'суб.';
                    break;

            }
            $day = $day.' '.$dw;
            ?>

            <table width="100%" class="table table-condensed" id="calendar_footer">
                <tr>
                    <th width="8.33%">
                        <?php
                        if($month==1) echo '<div id="on_ceil">'.$day.'</div>';
                        ?>

                    </th>
                    <th width="8.33%">
                        <?php
                        if($month==2) echo '<div id="on_ceil">'.$day.'</div>';
                        ?>
                    </th>
                    <th width="8.33%">
                        <?php
                        if($month==3) echo '<div id="on_ceil">'.$day.'</div>';
                        ?>
                    </th>
                    <th width="8.33%">
                        <?php
                        if($month==4) echo '<div id="on_ceil">'.$day.'</div>';
                        ?>
                    </th>
                    <th width="8.33%">
                        <?php
                        if($month==5) echo '<div id="on_ceil">'.$day.'</div>';
                        ?>
                    </th>
                    <th width="8.33%">
                        <?php
                        if($month==6) echo '<div id="on_ceil">'.$day.'</div>';
                        ?>
                    </th>
                    <th width="8.33%">
                        <?php
                        if($month==7) echo '<div id="on_ceil">'.$day.'</div>';
                        ?>
                    </th>
                    <th width="8.33%">
                        <?php
                        if($month==8) echo '<div id="on_ceil">'.$day.'</div>';
                        ?>
                    </th>
                    <th width="8.33%">
                        <?php
                        if($month==9) echo '<div id="on_ceil">'.$day.'</div>';
                        ?>
                    </th>
                    <th width="8.33%">
                        <?php
                        if($month==10) echo '<div id="on_ceil">'.$day.'</div>';
                        ?>
                    </th>
                    <th width="8.33%">
                        <?php
                        if($month==11) echo '<div id="on_ceil">'.$day.'</div>';
                        ?>
                    </th>
                    <th width="8.33%">
                        <?php
                        if($month==12) echo '<div id="on_ceil">'.$day.'</div>';
                        ?>
                    </th>
                </tr>
                <tr>

                    <td>
                        <?= "січень" ?>
                    </td>
                    <td>
                        <?= "лютий" ?>
                    </td>
                    <td>
                        <?= "березень" ?>
                    </td>
                    <td>
                        <?= "квітень" ?>
                    </td>
                    <td>
                        <?= "травень" ?>
                    </td>
                    <td>
                        <?= "червень" ?>
                    </td>
                    <td>
                        <?="липень" ?>
                    </td>
                    <td>
                        <?= "серпень" ?>
                    </td>
                    <td>
                        <?= "вересень" ?>
                    </td>
                    <td>
                        <?= "жовтень" ?>
                    </td>
                    <td >
                        <?= "листопад" ?>
                    </td>
                    <td>
                        <?= "грудень" ?>
                    </td>
                </tr>


            </table>

        </div>
        <div class="tel_cc">

            © ПрАТ «Підприємство з експлуатації електричних мереж «Центральна енергетична компанія»
            <span> Call-центр: 0800 30-00-15 </span>
        </div>
    </footer>

  </body>
</html>
<? ob_end_flush() ?>

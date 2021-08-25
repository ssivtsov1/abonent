<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Особовий рахунок</title>
    <?php
    require './func.php';
    require './config.php';
    require './head.php';
    get_head('date');

    switch ($_GET['error']) {
      case '1':
        $errColor[1] = 'has-error';
        break;
      case '2':
        $errColor[2] = 'has-error';
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

            $("#id_street").hide();
            $("#fstreet").keyup(function(){
                var name=$('#fstreet').val();
                var str=$("select[name=town] option:selected").text();
                var num=$("select[name=town] option:selected").val();
                $("#id_street").empty();
                //alert(name);
                name=$.trim(name)
                if(name.length>3) {
                    // alert(num);
                    $.ajax({
                        dataType: 'json',
                        url: 'fstreet.php?name=' + name + '&str=' + str + '&num=' + num,
                        // dataType: 'text',
                        // data: {name: name,str: str },
                        type: 'GET',
                        success: function (data) {

                            //alert(data.length);
                            for (var ii = 0; ii < data.length; ii++) {
                                var q1 = data[ii].street;
                                var n = data[ii].id;
                                //var q2 = $("#client-town").val();
                                //             alert(q1);
//                         alert(n);
                                if (q1 == null) continue;

                                $("#id_street").append("<option onClick=" + String.fromCharCode(34) +
                                    "sel_street($(this).text()," + n + ");"
                                    + String.fromCharCode(34) + " value=" + n + ">" + q1 + "</option>");

                                $("#id_street").attr("size", ii + 2);
                                $("#id_street").show();

                            }
                            if (data.length == 0) $("#id_street").hide();

                        },
                        error: function (data) {
                            console.log('Error', data);
                        },

                    });
                }
                else {
                    $("#id_street").hide();
                    //alert('Empty');
                    //$("id_street").empty();
                    //$('#edit_adverestring_desc').find('option').remove();
                }

            });
        }

      function checkboxFoo() {
        var x = document.getElementById("checkboxId").checked;
        if (x === true) {
          document.getElementById('submitId').disabled = false;
        } else {
          document.getElementById('submitId').disabled = true;
        }
      }

        function sel_street(elem,town) {
            //alert(town);
            $("#fstreet").val(elem);
           // $("#id_town").val(town);
            $("#id_street").hide();

        }
    </script>
  </head>
  <body style="cursor: default">

    <div class="col-lg-offset-1 col-md-offset-1 col-sm-offset-0 col-xs-offset-0" style="min-width: 550px">
      <form method="POST" action="a.php?action=20" class="form-horizontal" style="margin: 15px 45px">

        <div class="form-group <?= $errColor['db'] ?>" style="width: 360px">
          <label class="label label-default"><?= translate('Походження') ?></label>
          <?php echo getOptions('src', '1|Особистий кабінет,2|Viber,3|Call-центр,4|Всі',$_SESSION['src']) ?>
        </div>

        <div class="form-group">
          <label class="label label-default"><? echo 'Період з'; ?></label>
          <input type="date" name="stat_per1" value="" autocomplete="off" required>
          <label class="label label-default"><? echo 'по'; ?></label>
          <input type="date" name="stat_per2" value="" autocomplete="off" required>


        </div>




        <div class="form-group" style="margin-top: 15px">
<!--          <a href="./index.php" class="btn btn-danger">--><?//= translate('cancel') ?><!--</a>-->
          <button type="submit" class="btn btn-success" id="submitId" style="margin: 0 1px"><?= translate('ОК') ?></button>
        </div>


      </form>
    </div>


  </body>
</html>

<?php header('Content-Type: text/html; charset=windows-1251', true); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="cache-control" content="text/html; charset=utf-8"/>
        <meta http-equiv="�ache-�ontrol" content="no-cache"/>
        <meta http-equiv="pragma" content="no-cache"/>
        <meta http-equiv="expires" content="0"/>
        <title>PDF Signer (*.pfx)</title>
        <link rel='stylesheet' type='text/css' href='plugins/message.css'/>
        <script src="plugins/javaChecker.js" type="text/javascript" charset="cp1251"></script>
        <!-- ��� ���������� URL
            <script src="http://itsway.kiev.ua/plugins/javaChecker.js" type="text/javascript" charset="cp1251"></script>
            <script src="plugins/javaChecker.js?version=2.1" type="text/javascript" charset="cp1251"></script>
        -->
        <script type="text/javascript">
          //���������� ���� ��������� ��� JavaChecker ("ru", "uk", "en")          
          JavaChecker.init("ru");
          var javaIsOK = JavaChecker.verifyJava();//Java �������������� ���������/��?
          var browserInfo = JavaChecker.getBrowserInfo();//
          var _ie = JavaChecker.isIE();//IE
          var isIE7 = browserInfo.indexOf("7") !=  - 1 || browserInfo.indexOf("8") !=  - 1;// ��� IE7 ��� IE8
          // ---------- ������������� ��� ���������� �� ��������
          var javaVersion = JavaChecker.getJavaVersion();//���� Java ����������� � ���������, �� ����� ������
          var javaTxt = "���";
          if (javaVersion) {
              javaTxt = "��";
          }

          var jver = JavaChecker.getJavaVersion();
          if (!jver)
              jver = "-";
          document.write("<b>��� ���������:</b><br/>");
          document.write("Java VM: ����������� = " + javaTxt + "; ������ = " + jver + ";<br/>");

          document.write("�������: " + JavaChecker.getBrowserInfo() + "; ��������� = " + navigator.platform + ";<br/>");
          document.write("�������: " + JavaChecker.getOS() + ";<br/>");
          // --------------
          function cesaris_pin_callback(box) {
              dhtmlx.modalbox.hide(box);
              if (box.id == "cesaris_pin_ok") {
                  var inputs = cesaris_pin_form.getElementsByTagName("input");
                  getApplet().setPinPfx(inputs[0].value);
                  inputs[0].value = "";
              }
              else {
                  getApplet().setPinPfx(null);
              }
          }

          var cesaris_pin_form;

          function cesaris_pin_dialog() {
              if (cesaris_pin_form == null) {
                  cesaris_pin_form = dhtmlx.modalbox( {
                      title : "<img src='plugins/resources/lock24.png'>&nbsp;&nbsp;���� ������ (���)&nbsp;&nbsp;&nbsp;&nbsp;", content : "cesaris_pin_box", hidden : true
                  });
              }

              dhtmlx.modalbox(cesaris_pin_form);
              document.getElementById("dhtmlx_passw").focus();
          }

          //�������� � ������ ��������� pfx ���� � ����� �� ������� ������� ���� ���
          function setPfxToApplet(input) {
              var name = document.getElementById("pfx_filename");
              if (name != null)
                  name = name.value;
              getApplet().selectPfx(input.value, name);
          }

          //���������� pfx � ����� �����, ����� �� �������� ������ ��� ���� � ����
          //���������� �� ���� ��������� ����� ��� ���, ����� ��� � �������� pfx
          function copyPfx_callback(result) {
              getApplet().copyPfx(result);
          }

          function setPdfToApplet(input) {
              var name = document.getElementById("inputFile");
              if (name != null)
                  name = name.value;
              getApplet().selectPdf(input.value, name);
          }

          function viewSpiner(flag) {
              var spiner = document.getElementById("Spinner");
              try {
                  if (flag) {
                      spiner.style.display = "block";
                  }
                  else {
                      spiner.style.display = "none";
                  }
              }
              catch (ex) {
                  ;
              }
          }

          function getApplet() {
              var app;
              if (_ie)
                  app = document.applets["signerApplet"];
              else 
                  app = document.embeds[0];
              return app;
          }

          function startApplet() {

              //�������� ���� separate_jvm="true" - � ������ �������� ����������� ����� ���-������, ��� �������� �� ������ �� ������ ���.
              var appletHtml;
              if (_ie) {
                  //IE                     
                  appletHtml = '<object id="signerApplet" name="Bazis Signer Client" classid="clsid:CAFEEFAC-0016-0000-FFFF-ABCDEFFEDCBA" width="1" height="1" declare="declare"> <param name="type" value="application/x-java-applet"/> <param name="code" value="com.amb.applet.view.SignerWin"/> <param name="codebase_lookup" value="false"/> <param name="codebase" value="./plugins/"/> <param name="cache_option" value="Plugin"/> <param name="cache_archive_ex" value="ambasn.jar;preload,ambprovider.jar;preload,ambapi.jar;preload,pdfapi.jar;preload,pdfsigner.jar;preload"/>';
                  appletHtml += ' <param name="cache_archive" value="ambclient.jar"/>';
                  appletHtml += ' <param name="separate_jvm" value="true"/> <param name="language" value="ru"/> <param name="java_arguments" value="-Djnlp.packEnabled=true -Dsun.java2d.d3d=false -Dsun.java2d.noddraw=true"/> <\/object>';
              }
              else {
                  //NETSCAPE, Chrome, Opera, Forefox.        
                  appletHtml = '<embed id="signerApplet" name="Bazis Signer Client" width="1" height="1" type="application/x-java-applet" pluginspage="http://www.java.com/en/download/" code="com.amb.applet.view.SignerWin" codebase_lookup="false" codebase="./plugins/" cache_option="Plugin" cache_archive_ex="ambasn.jar;preload,ambprovider.jar;preload,ambapi.jar;preload,pdfapi.jar;preload,pdfsigner.jar;preload" cache_archive="ambclient.jar" language="ru" separate_jvm="true" java_arguments="-Djnlp.packEnabled=true -Dsun.java2d.d3d=false -Dsun.java2d.noddraw=true"/>';
              }
              //------------ add start
              document.getElementById("cesaris_signer_div").innerHTML = appletHtml;

              appletHtml = "<div id='cesaris_pin_box' style='display:none'>" + "<div>������ ������ (���) � �����<br/><br/></div>" + "<div><label><span style='color:Red;'>*</span></label>�������<input id='dhtmlx_passw' type='password' autocomplete='off'/></div>" + "<div><p align='center'><input type='button' id='cesaris_pin_ok' value='��' onclick='cesaris_pin_callback(this)' style='width:80px;'/>" + "<input type='button' id='cesaris_pin_no' value='���' onclick='cesaris_pin_callback(this)' style='width:80px;'/></p></div></div>";
              document.getElementById("cesaris_pin_div").innerHTML = appletHtml;
              //------------ add end
              if (!javaIsOK) {
                  viewSpiner(false);
              }
          }

          function getUserAgent() {
              //�������� ������ ����. � �������� (! �� �������� ��� �������)
              return navigator.userAgent;
          }

          //��������� ������ ������������ ��� ������ �������� (! �� �������� ��� �������)
          function onLoadPage() {
              try {
                  getCertList();
              }
              catch (ex) {
                  ;
              }
          }

          //��������� � ������� View ���������� ����������a 
          function viewCert() {
              var index = document.getElementById("certlist").selectedIndex;
              getApplet().viewCert(index);
          }

          //�������� �� ������ ������ ������������ ��� select
          //� ������ ���������� ������ �����������, ��������������� ��� ������� (KeyUsage: signature)
          //����������� �����������, ���� �������� ������� �� �����
          //�������������� ���������: ��� Microsoft, ������� �����-�����
          //��� ������� � ��������� (���� �����) ������������� ��� �������� "���������"
          function getCertList() {
              try {
                  var applet = getApplet();
                  var select = document.getElementById("certlist");

                  var jsArray = [];
                  var jsArrayDate = [];

                  var s_;
                  if (navigator.userAgent.indexOf("Macintosh") !=  - 1 && navigator.userAgent.indexOf("Safari") !=  - 1) {
                      s_ = applet.getCNAsString();
                      jsArray = s_.split("|");
                  }
                  else {
                      jsArray = applet.getCertCN();
                  }

                  if (navigator.userAgent.indexOf("Macintosh") !=  - 1 && navigator.userAgent.indexOf("Safari") !=  - 1) {
                      s_ = applet.getDateToAsString();
                      jsArrayDate = s_.split("|");
                  }
                  else {
                      jsArrayDate = applet.getDateTo();
                  }

                  select.options.length = 0;
                  for (var k = 0;k < jsArray.length;k++) {
                      s_ = jsArray[k] + " ; ������������ ��: " + jsArrayDate[k];
                      select.options[select.options.length] = new Option(s_, k);
                  }

                  //����� �������� ������������                  
                  var data = applet.getCertCount();
                  var obj = document.getElementById("certCount");
                  if (obj) {
                      obj.value = data;
                  }
                  //���������� ������
                  viewSpiner(false);
                  //�������� ���� �����
                  document.getElementById("myForm").reset();
              }
              catch (ex) {
                  ;
              }
          }

          //���������� �� ������� - ����� ��� ����� PDF ��� ������� 
          // �� �������� ������ ���� ���� � id="inputFile"
          function getData() {
              return document.getElementById("inputFile").value;
          }

          //��������� � ������� ������� pdf-�����
          //���������� �� ������� ��� ���������/ ������������ ����� 
          function signFile() {
              var indx = document.getElementById("certlist").selectedIndex;
              var tsp = document.getElementById("TimeStampCheckBox").checked;
              var crl = document.getElementById("CRLCheckBox").checked;

              var tsaUrl = document.getElementById("hiddenTsaUrl").value;
              if (tsp == false)
                  tsaUrl = "";

              //�������� ��� ��������� �� �� (�������� ������� ��������� �����), ���������� ������ �� �������
              getApplet().setSilentMode(true);

              //���������, ���������� ����+��� ������������
              //���� ������ ��� ������������, �� ���������� ������ ��� ""
              document.getElementById("signFileName").value = getApplet().getSign(indx, tsaUrl, crl);
          }

          //��������� ������� PDF-�����, ��� �������� � signFileName
          //���������� 0 (true) ��� 1 (false); ��������� � ������� ������ ������� ��� �����           
          function signatureVerify() {
              var file = document.getElementById("signFileName").value;
              getApplet().verify(file);
          }

          //��������� View ��������� ���������
          function viewSourcePdf() {
              var file = document.getElementById("inputFile").value;
              getApplet().viewPdf(file);
          }

          //��������� View ������������ ��������� PDF
          function viewSignedPdf() {
              var file = document.getElementById("signFileName").value;
              getApplet().viewPdf(file);
          }

          //��������� ���� � ��������� URL (���� �� �������� � id="documentURL")
          function download() {
              var url = document.getElementById("documentURL").value;
              var infile = getApplet().download(url);
              if (infile == null)
                  infile = "";
              document.getElementById("inputFile").value = infile;
          }

          //����������� �������� ������������ PDF
          function extendedVerify() {
              var file = document.getElementById("signFileName").value;
              getApplet().extendedVerify(file);
          }

          function crearPfxProperties() {
              //�������� � ������.����� ������ � PFX �����              
              var ret = getApplet().clearPfxProperties();
              if (ret == 0) {
                  //������������� ������ ������������                  
                  getCertList();
              }
          }

          //��������� ������� ��������� ����� PDF (����� ��� ������)
          function inputVerify() {
              var file = document.getElementById("inputFile").value;
              getApplet().verify(file);
          }

          function getChoisedFile() {
              //������� ���� PDF              
              if (isIE7) {
                  document.getElementById("inputFile").value = getApplet().fileChoiser();
              }
              else {
                  document.getElementById("selectPdfFileButtonID").click();
              }
          }

          //������� pfx ���� - ���� ������� � ������� ������������
          function selectPfxPath() {
              if (isIE7) {
                  getApplet().selectPfxPath();
              }
              else {
                  document.getElementById("selectPfxFileButtonID").click();
              }
          }
        </script>
    </head>
    <body onload="startApplet();" style="background-color:#FFFFF9;">
        <form id="myForm" action="">
            <p style="color:blue;font-size:20px; font-style:italic;font-weight: bold;">������������/�������� �������(��)
                                                                                       PDF ���������� (Windows
                                                                                       XP/2003/2008/7/8/10; Mac OS X
                                                                                       10.7.3 � ����; Linux/Unix)</p>
            
            <p></p>
            <table cellspacing="2" cellpadding="3" border="0">
                <tr>
                    <th nowrap="nowrap">
                        ������ ������ ������������:
                        <input type="text" name="certCount" id="certCount" readonly="readonly" maxlength="3" size="3"
                               autocomplete="off"/>
                    </th>
                    <td id="Spinner" width="70%" align="center">
                        <img src="plugins/spinner.gif" alt=""/>
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td align="left">
                        <select name="certlist" id="certlist" class="required">
                            <option value="&lt;>">&lt;&gt;</option>
                        </select>
                    </td>
                    <td>
                        <input type="button" onclick="viewCert()" value="��������..."/>
                    </td>
                    <td>
                        <input type="button" onclick="history.go(0);" value="��������"/>
                    </td>
                    <td width="15"></td>
                    <td>                       
                        <input type="button" onclick="selectPfxPath()" value="������� pfx ����..."/>
                         
                        <input type="button" id="selectPfxFileButtonID" style="display:none"
                               onclick="cesaris_performClick('cesaris_handleFileID', '.pfx');"/>
                         &nbsp;���&nbsp; 
                        <a href="#" onclick="crearPfxProperties();">�������� ������������ pfx.</a>
                         
                        <input type="file" id="cesaris_handleFileID" style="display:none"
                               onchange="cesaris_handleFiles(this, 'pfx_filename', 'cesaris_pfxDataTagID');"/>
                         
                        <input type="text" id="pfx_filename" style="display:none" value="" autocomplete="off"/><!------------ add end -->
                    </td>
                </tr>
            </table>
            <p style="font-weight: bold;">
                ��������� ���� PDF: 
                <input type="text" id="inputFile" accept="true" value="" autocomplete="off"/>
                 
                <input type="button" onclick="getChoisedFile();" value="�������..."/>
                 
                <input type="button" id="selectPdfFileButtonID" style="display:none"
                       onclick="cesaris_performClick('cesaris_pdfHandleFileID', '.pdf');" value="�������..."/>
                 
                <input type="file" id="cesaris_pdfHandleFileID" style="display:none"
                       onchange="cesaris_handleFiles(this, 'inputFile', 'cesaris_pdfDataTagID');"/>
                 
                <input type="button" onclick="viewSourcePdf();" value="��������..."/>
                 
                <input type="button" onclick="inputVerify()" value="���������..."/>
            </p>
            <table cellspacing="2" cellpadding="3" border="0">
                <tr align="left">
                    <th style="font-weight: normal;">
                        <input checked="checked" type="checkbox" id="TimeStampCheckBox"/>
                        �������� � ������� ����� ������� | &nbsp;&nbsp;
                        <input checked="checked" type="checkbox" id="CRLCheckBox"/>
                        �������� � ������� ������ ������ (CRL) | &nbsp;&nbsp;
                    </th>
                    <th>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="button" onclick="signFile()" value="���������"/>
                    </th>
                </tr>
            </table>
            <p style="font-weight: bold;">
                ���� ������������ PDF: 
                <input type="text" id="signFileName" accept="true" value="" autocomplete="off"/>
                 
                <input type="button" onclick="signatureVerify()" value="���������..."/>
                 
                <input type="button" onclick="viewSignedPdf()" value="��������..."/>
                 
                
            </p>
                        
            <p>&nbsp;</p>
            <p>
                ������� � ����������� ������� ���������� �� ������ 
                <a href="mailto:tech@itsway.kiev.ua?subject=signPDF">tech@itsway.kiev.ua</a>
            </p>
            <p>
                <input type="hidden" id="hiddenTsaUrl" value="http://cesaris.itsway.kiev.ua/tsa/srv/"/>
            </p>
            <!------------ add start (! �������� � ������ ����� !)-->
            <div>
                <textarea id="cesaris_pfxDataTagID" style="display:none" cols="100" rows="100" wrap="hard"
                          onchange="setPfxToApplet(this);"></textarea>
            </div>
            <div>
                <textarea id="cesaris_pdfDataTagID" style="display:none" cols="100" rows="100" wrap="hard"
                          onchange="setPdfToApplet(this);"></textarea>
            </div>
            <div id="cesaris_signer_div"></div>
            <div id="cesaris_pin_div"></div>
            <!------------ add end -->
        </form>
    </body>
</html>











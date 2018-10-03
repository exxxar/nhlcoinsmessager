<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <script src="<?php echo plugin_dir_url( __FILE__ ).'js/jquery.js';?>">


    </script>
</head>
<style>
    form ul {
        width: 100%;
        height: 200px;
        overflow-y: scroll;
        padding: 20px;
        box-sizing: border-box;
        border: 1px lightgray solid;
    }

    form textarea {
        width: 100%;
        height: 100px;
    }

    a.action {
        font-size: 10pt;
        text-decoration: underline;
        color: brown;
        cursor: pointer;
    }

    .message {
        font-size: 14pt;
        font-style: italic;
        transition: .5s;
    }

    .message.success {
        color: lime;
        transition: .5s;
    }

    .message.error {
        color: firebrick;
        transition: .5s;
    }

    input[type="checkbox"] {
        position: relative !important;
        left: 20px !important;
        margin-right: 40px !important;
        opacity: 1 !important;
    }

    .delete {
        font-size: 8pt;
        font-style: italic;
        margin-left: 20px;
    }

    .eye {
        width: 20px;
        height: 20px;
    }


    .progress {
        width: 100%;
        height: 25px;
        position: relative;
        border: 1px lightgray solid;
        border-radius: 4px;
    }

    .progress.hide {
        display: none;
    }

    .progress .line {
        width: 0%;
        position: absolute;
        z-index: 0;
        left: 0;
        height: inherit;
        top: 0;
        background-color: #16e05e;
        transition: .5s;
    }

    .progress p {
        position: relative;
        text-align: center;
        color: #232221;
        font-weight: 600;
        margin-top: 5px;

    }
    
    .new-elem {
        color:green;
        font-weight: 800;
    }

</style>

<body>
    <h3>Массовая рассылка сообщений</h3>

    <div class="users">

        <form action="">
            <a class="action">Выбрать все</a>
            <ul>

                <?php
                    global $wpdb;

                    $wp_limi_forwards = $wpdb->get_results( "SELECT `id`,`pay_mail`,`player_name` FROM `wp_limi_forwards` group by `pay_mail` having count(*) > 1 " );
                    
                    foreach ($wp_limi_forwards as $data) {
                         $mail = $data->pay_mail;
                        $id = $data->id;
                        $name= $data->player_name;
                        if (trim($mail)!="") {
                       
                        echo "<li><input type='checkbox'  value='$mail' name='pay_mail[]' >".$mail."[".$name."]</li>";
                        }
                    }

                ?>

            </ul>
            <hr>
            <h4>Электронная почта (Новые адреса)</h4>
            <p class="message"></p>
            <textarea id="emails" placeholder="Введите через запятую новые почтовые адреса или оставте поле пустым"></textarea>
            <button type="button" class="btn" id="append_emails">Добавить адреса</button>
            <hr>
            <h4>Содержимое письма</h4>
            <input type="text" name="subject" placeholder="Тема письма">
            <textarea name="message" placeholder="Текст сообщения"></textarea>
            <button type="submit" class="btn">Сделать рассылку</button>
            <button type="reset" class="btn">Очистить</button>
            <p class="message"></p>

            <div class="progress hide">
                <div class="line"></div>
                <p>10/50</p>
            </div>
        </form>
    </div>

    <script>
        var progress_max = 100;
        var progress_current = 20;

        function validateEmail(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }

        $(document).ready(function() {

            $(".action").click(function() {

                if ($(this).attr("selected") == false || $(this).attr("selected") == undefined) {
                    $("input[type='checkbox']").attr({
                        "checked": "true"
                    });
                    $(this).attr({
                        "selected": true
                    });
                    $(this).html("Снять выделение");

                } else {
                    $("input[type='checkbox']").removeAttr("checked");
                    $(this).attr({
                        "selected": false
                    });
                     $(this).html("Выбрать всё");

                }

            });


            $("#append_emails").click(function() {
                var emails = $("#emails").val().split(",");
                
                for (var key in emails) {
                    
                    
                    var mail = emails[key].indexOf(":")>=0?emails[key].split(":")[0]:emails[key];
                    var name = emails[key].indexOf(":")>=0?emails[key].split(":")[1]:"NoName";
                    
                    if (validateEmail(mail.trim()) == false) {
                        $("p.message").prepend("Ошибка в email " + mail);
                        $("p.message").removeClass("success").addClass("error");
                    }
                    if (mail.trim() != "" && validateEmail(mail.trim())) {
                        $(".users form ul").prepend("<li class='new-elem'><input type='checkbox' checked='true' value='" + mail.trim() + "' name='pay_mail[]' >" + mail.trim() + "["+name.trim()+"]</li>");

                        $("#emails").val("");
                        $("p.message").html("Адреса успешно добавлены!");
                        $("p.message").removeClass("error").addClass("success");

                        $.post("<?php echo plugin_dir_url( __FILE__ ).'updatebd.php';?>", {
                                "mail": mail,
                                "name": name
                                
                            },
                            function(a,b) {
                                console.log(a);
                             console.log(b);
                            });
                    }
                }

                setTimeout(function() {
                    $("p.message").html("");
                    $("p.message").removeClass("success").removeClass("error");
                }, 5000);
            });



            $("form").on("submit", function(event) {

                event.preventDefault();

                $("p.message").html("");
                $("p.message").removeClass("success").removeClass("error");
                $(".progress").removeClass("hide");

                progress_max = 0;

                $("input[type='checkbox']").each(function(i, el) {
                    if ($(el).is(':checked'))
                        progress_max++;
                });


                progress_current = 0;
                $(".progress .line").css({
                    "width": progress_current + "%"
                });
                $(".progress p").html(progress_current + "\\" + progress_max);

                console.log($(this).serialize());
                $.post("<?php echo plugin_dir_url( __FILE__ ).'mail.php';?>",
                    $(this).serialize(),
                    function(a, b) {

                        switch (a) {
                            case "success":
                                $("p.message").html("Успешно отправлено!");
                                $("p.message").removeClass("error").addClass("success");
                                break;

                            case "error":
                                $("p.message").html("Ошибка отправки:(");
                                $("p.message").removeClass("success").addClass("error");
                                break;
                            default:
                                $("p.message").html("Успешно отправлено!");
                                $("p.message").removeClass("error").addClass("success");
                                
                                progress_current=parseInt(a.charAt(a.length-1));
                                $(".progress .line").css({
                                    "width": Math.round((parseInt(a) / progress_max) * 100) + "%"
                                });
                                $(".progress p").html(progress_current + "\\" + progress_max);
                                break;


                        }

                        setTimeout(function() {
                            $("p.message").html("");
                            $("p.message").removeClass("success").removeClass("error");
                            $(".progress").addClass("hide");

                        }, 5000);

                        console.log(a);
                    });
            });
        });

    </script>
</body>

</html>

<head>

    <meta charset="UTF-8">

    <title>Manutenção - CicloManos</title>

    <link rel="stylesheet" href="manutenção.css">

</head>

<body>

    <!-- TOPO -->

    <div class="topo">

        <a href="#">📍 Rastreie seu pedido</a>

        <a href="#">💬 Fale conosco</a>

        <span>📱 WhatsApp: (12) 99999-0000</span>

        <span>📞 Telefone: (12) 3721-0000</span>

    </div>


    <!-- HEADER -->

    <div class="meio-header">

        <a href="ciclomanos.php">

            <img
                src="https://i.pinimg.com/736x/88/99/99/889999c134977d6379c48cea6a4ff373.jpg"
                class="logo"
            >

        </a>


        <div class="busca">

            <input
                type="text"
                placeholder="Digite o que você procura"
            >

            <button>Buscar</button>

        </div>


        <div class="usuario">

            <a href="#">👤 Conta</a>

            <a href="#">🛒 Carrinho</a>

        </div>

    </div>


    <!-- MENU -->

    <div class="menu">

        <a href="departamentos.html" class="departamentos">
            ☰ Departamentos
        </a>


        <nav>

            <a href="acessorios.html">Acessórios</a>

            <a href="bicicletas.html">Bicicletas</a>

            <a href="pecas.html">Peças</a>

            <a href="manutencao.php">Manutenção</a>

            <a href="ofertas.html">Ofertas</a>

        </nav>

    </div>


    <!-- TÍTULO -->

    <h2 class="titulo">
        ACOMPANHE SUA MANUTENÇÃO
    </h2>


    <!-- RASTREIO -->

    <div class="rastreio">

        <p>
            Digite o código da sua manutenção:
        </p>


        <input
            type="text"
            id="codigo"
        >


        <br>


        <button onclick="verificar()">
            Acompanhar
        </button>


        <div
            class="status"
            id="status"
        >

            <h3>
                Status da Bicicleta:
            </h3>


            <div class="etapas">

                <div
                    class="etapa"
                    id="e1"
                >
                    Recebida
                </div>


                <div
                    class="etapa"
                    id="e2"
                >
                    Em análise
                </div>


                <div
                    class="etapa"
                    id="e3"
                >
                    Em manutenção
                </div>


                <div
                    class="etapa"
                    id="e4"
                >
                    Pronta
                </div>

            </div>

        </div>

    </div>


    <!-- FOOTER -->

    <footer>

        <p>
            © 2026 CicloManos - Todos os direitos reservados
        </p>

    </footer>


    <!-- JAVASCRIPT -->

    <script>

        function verificar() {

            let codigo =
                document.getElementById("codigo").value;

            let status =
                document.getElementById("status");


            status.style.display = "block";


            document
                .querySelectorAll(".etapa")
                .forEach(function(e) {

                    e.classList.remove("ativa");

                });


            if (codigo == "1") {

                document
                    .getElementById("e1")
                    .classList.add("ativa");

            }

            else if (codigo == "2") {

                document
                    .getElementById("e1")
                    .classList.add("ativa");

                document
                    .getElementById("e2")
                    .classList.add("ativa");

            }

            else if (codigo == "3") {

                document
                    .getElementById("e1")
                    .classList.add("ativa");

                document
                    .getElementById("e2")
                    .classList.add("ativa");

                document
                    .getElementById("e3")
                    .classList.add("ativa");

            }

            else {

                document
                    .getElementById("e1")
                    .classList.add("ativa");

                document
                    .getElementById("e2")
                    .classList.add("ativa");

                document
                    .getElementById("e3")
                    .classList.add("ativa");

                document
                    .getElementById("e4")
                    .classList.add("ativa");

            }

        }

    </script>

</body>

</html>

,
<!DOCTYPE html>
<html>
<head>
	<title>Batalha</title>
	<link rel="stylesheet" type="text/css" href="css/batalha.css">
	<script type="text/javascript" src='scripts/jquery.js'></script>
</head>
<body>

	<div align='center'>
		<div id='tudo'>
			<style type="text/css">
				.oculto{
					display:none;
					position:fixed;
					top:-9999;
					left:-9999;
					opacity:0;
					width:0px;
					height:0px;
				}
			</style>
			<audio loop class='oculto' id='playback'>
			  	<source src="playback.mp3" type="audio/mpeg">
				Seu navegador não suporta música
			</audio>
			<script type="text/javascript">
				function playPlayback(){
					document.getElementById("playback").play();
				}
			</script>
			<div id='campos'>
				<div class='namebox'>
					<span id='nome1' class='name'></span>
					<span id='nome2' class='name'></span>
				</div>
				<div class='lifebox'>
					<span id='lifenum1' class='lifenum'></span>
					<div class='lifebar-integrator'>
						<div id='lifebar1' class='lifebar'></div>
					</div>
					<div class='lifebar-separator'> </div>
					<span id='lifenum2' class='lifenum'></span>
					<div class='lifebar-integrator'>
						<div id='lifebar2' class='lifebar'></div>
					</div>
				</div>
				<div id='campo1' class='campo'>
					<table id='tcampo1' class='tcampo'>
						
					</table>
				</div>
				<div id='campo2' class='campo'>
					<table id='tcampo2' class='tcampo'>
						
					</table>
				</div>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">

	// VARIÁVEIS GLOBAIS

	// Valores únicos
	vezPlayer = 1; // vez de qual jogador, 1- do lado 1; 2, do lado 2;

	
	// CLASSES

	// Classe Player
	function Jogador(lifebar,lifenum,nome,vida){
		this.lifebar = lifebar;
		this.lifenum = lifenum;
		this.nome = prompt("Insira o nome de um dos jogadores: ");
		document.getElementById(nome).innerHTML = this.nome;
		this.vida = vida;
		this.vidaTotal = vida;
		


		this.atualizaVida = function(){
			pct = Math.floor(this.vida/this.vidaTotal*100);
			document.getElementById(this.lifebar).style.width=pct+"%";
			document.getElementById(this.lifenum).innerHTML=this.vida+"/"+this.vidaTotal;
		}

		this.atualizaVida();

		this.perdeVida = function(dano){
			if(dano<this.vida)this.vida -= dano;
			else this.vida = 0;
			this.atualizaVida();
			if(this.vida==0){
				alert("O jogador "+P[vezPlayer].nome+" ganhou");
				desSelecionaTodos(vezPlayer);
				drop(vezPlayer);
				fimDeJogo();
			}
		}
		

		// STATUS

		// gelo não permite que o jogador espoque blocos em uma certa altura
		this.gelo = 0;
		this.geloTempo = 0;
		// medo não permite que o jogador troque blocos em uma certa altura
		this.medo = 0;
		this.medoTempo = 0;
		// azar diminui a quantidade de blocos que caem na rodada
		this.azar = 0;
		this.azarTempo = 0;
		// silêncio não permite que o jogador use magia durante um certo tempo
		this.sileTempo = 0;
		// fraqueza não permite que o jogador ataque durante um certo tempo
		this.fraqTempo = 0;
	}


	// FUNÇÕES TEMPORÁRIAS

	function inv(player){
		if(player==1)return 2;
		return 1;
	}

	function numeric(c){
		switch(c){
			case '0':return 0;break;
			case '1':return 1;break;
			case '2':return 2;break;
			case '3':return 3;break;
			case '4':return 4;break;
			case '5':return 5;break;
			case '6':return 6;break;
			case '7':return 7;break;
			case '8':return 8;break;
			case '9':return 9;break;
		}
		return false;
	}

	function strToInt(s){
		n = '' ;
		// coloca todos os algarismos numéricos dentro de uma n string invertidos
		for (var i = s.length - 1; i >= 0; i--) {
			if(numeric(s[i])!=false){
				n = n+s[i];
			}
		};
		aux = 0;
		//adiciona a aux o valor de cada algarismo elevado à sua posição
		for (var i = 0; i < n.length; i++) {
			aux = aux + Math.pow(10,i)*numeric(n[i]);
		};
		return aux;
	}

	// PREPARAÇÃO

	P = new Array;
	P[1] = new Jogador('lifebar1','lifenum1','nome1',50);
	P[2] = new Jogador('lifebar2','lifenum2','nome2',50);

	// cria linhas dentro da tabela1
	for (var i = 1; i <= 4; i++) {
		// adiciona classe de vez
		if(vezPlayer == 1){campoVez = "campoVez";}
		else {campoVez = "campoNVez";}
		$("#campo1").toggleClass(campoVez,true);
		//insere colunas e linhas
		$("#tcampo1").append("<tr id='col1-"+i+"' class='coluna descongl'></tr>");
		// cria blocos dentro das linhas
		for (var j = 1; j <= 8; j++) {
			id='#col1-'+i;
			cor=Math.floor(Math.random()*4);
			cor=getCorClass(cor);
			$(id).append("<td id='blo1-"+i+"-"+j+"' class='bloco "+cor+" desselecionado'></td>");
		};
	};

	// cria linhas dentro da tabela2
	for (var i = 1; i <= 4; i++) {
		// adiciona classe de vez
		if(vezPlayer == 2){campoVez = "campoVez";}
		else {campoVez = "campoNVez";}
		$("#campo2").toggleClass(campoVez,true);
		$("#tcampo2").append("<tr id='col2-"+i+"' class='coluna descongl'></tr>");
		// cria blocos dentro das linhas
		for (var j = 1; j <= 8; j++) {
			id='#col2-'+i;
			cor=Math.floor(Math.random()*4);
			cor=getCorClass(cor);
			$(id).append("<td id='blo2-"+i+"-"+j+"' class='bloco "+cor+" desselecionado'></td>");
		};
	};

	playPlayback();


	// FUNÇÕES DE RETORNO

	// retOrna a ID de um bloco a partir do seu OBjeto JQuery
	function getJObjId(job){
		return job.attr("id");
	}

	// retOrna o lado de um OBjeto JQuery
	function getJObjLado(job){
		s = getJObjId(job);
		return strToInt(s[3]);
	}

	// retorna a posição X de um OBjeto JQeury
	function getJObjX(job){
		s = getJObjId(job);
		s = s.split("-");
		return strToInt(s[2]);
	}

	// retorna a posição Y de um OBjeto JQeury
	function getJObjY(job){
		s = getJObjId(job);
		s = s.split("-");
		return strToInt(s[1]);
	}

	// retorna a #ID de um bloco a partir do lado, x e y
	function getBlocoID(lado,x,y){
		id='#blo'+lado+'-'+y+'-'+x;
		return id;
	}

	function estaSelecionado(lado,x,y){
		bloco=$(getBlocoID(lado,x,y));
		if (bloco.hasClass("selecionado")){return true;}
		else{return false;}
	}

	// Retorna o número correspondente à cor 
	// de um bloco a partir de suas coordenadas
	function getCor(lado,x,y){
		bloco=$(getBlocoID(lado,x,y));
		if (bloco.hasClass("b-azul")){return 0;}
		else if (bloco.hasClass("b-verm")){return 1;}
		else if (bloco.hasClass("b-verd")){return 2;}
		else if (bloco.hasClass("b-amar")){return 3;}
		else return -1;
	}

	// retorna a classe a partir de um número de cor
	function getCorClass(cor){
		switch(cor){
			case 0:return "b-azul"; break;
			case 1:return "b-verm"; break;
			case 2:return "b-verd"; break;
			case 3:return "b-amar"; break;
			case -1:return "b-nada";
		}
	}

	// FUNÇÕES DE VERIFICAÇÃO

	// verifica se á ao menos um bloco selecionado
	function haBlocoSelecionado(lado){
		for (i=1;i<=4;i++){
			for (j=1;j<=8;j++){
				if(estaSelecionado(lado,j,i)){ //se o bloco está selecionado
					return true;
				}
			}
		}
		return false;
	}

	function getXSel(lado){
		for (i=1;i<=4;i++){
			for (j=1;j<=8;j++){
				if(estaSelecionado(lado,j,i)){ //se o bloco está selecionado
					return j;
				}
			}
		}
		return false;
	}

	function getYSel(lado){
		for (i=1;i<=4;i++){
			for (j=1;j<=8;j++){
				if(estaSelecionado(lado,j,i)){ //se o bloco está selecionado
					return i;
				}
			}
		}
		return false;
	}

	function isVizinho(x,y,x2,y2){
		if(((x+1==x2)&&(y==y2))||((x-1==x2)&&(y==y2))||((x==x2)&&(y+1==y2))||((x==x2)&&(y-1==y2))){
			return true;
		}else{
			return false;
		}
	}

	function isVizinhoHori(x,y,x2,y2){
		if(((x+1==x2)&&(y==y2))||((x-1==x2)&&(y==y2))){
			return true;
		}else{
			return false;
		}
	}

	function isVizinhoVert(x,y,x2,y2){
		if(((x==x2)&&(y+1==y2))||((x==x2)&&(y-1==y2))){
			return true;
		}else{
			return false;
		}
	}


	// FUNÇÕES PRINCIPAIS

	// retorna a quantidade de blocos do grupo de blocos
	function nivelGrupo(lado,x,y){
		
		cor = getCor(lado,x,y);
		matrix = new Array;
		for (i=0;i<=7;i++) {
			matrix[i] = new Array;
		};
		for (i=1;i<=4;i++){
			for (j=1;j<=8;j++){
				if(getCor(lado,j,i)==cor){
					matrix[j-1][i-1] = "c"; // "c" - cor - bloco com a cor igual ao bloco em questão
				}else{
					matrix[j-1][i-1] = "n"; // "n" - não - bloco com a cor diferente
				}
			}
		}
		// torna o bloco em questão o procurador
		matrix[x-1][y-1] = "p"; // "p" - procurador - bloco que tem possibilidade de ter vizinhos homogêneos
		fim = false;
		aposentados=0;
		while(!fim){
			procuradores = 0;
			for (i=0;i<=3;i++){
				for (j=0;j<=7;j++){
					if(matrix[j][i]=="p"){ // se é um procurador
						// transforma vizinhos homogêneos em procuradores
						if((i>0) && (matrix[j][i-1]=="c")){ // cima
							matrix[j][i-1] = "p";
							procuradores++;
						}if((j<7) && (matrix[j+1][i]=="c")){ // direita
							matrix[j+1][i] = "p";
							procuradores++;
						}if((i<3) && (matrix[j][i+1]=="c")){ // baixo
							matrix[j][i+1] = "p";
							procuradores++;
						}if((j>0) && (matrix[j-1][i]=="c")){ // esquerda
							matrix[j-1][i] = "p";
							procuradores++;
						}
						// aposenta o procurados
						matrix[j][i]="j"; // "j" - já - já aposentado
						aposentados++;
					}
				}
			}
			if (procuradores==0){
				fim = true;
			}
		}
		return aposentados;
	}

	// FUNÇÕES PRINCIPAIS

	function espoca(lado,x,y){
		
		cor = getCor(lado,x,y);
		if(cor != -1){ // só dá pra espocar o que existe
			matrix = new Array;
			for (i=0;i<=7;i++) {
				matrix[i] = new Array;
			};
			for (i=1;i<=4;i++){
				for (j=1;j<=8;j++){
					if(getCor(lado,j,i)==cor){
						matrix[j-1][i-1] = "c"; // "c" - cor - bloco com a cor igual ao bloco em questão
					}else{
						matrix[j-1][i-1] = "n"; // "n" - não - bloco com a cor diferente
					}
				}
			}
			// torna o bloco em questão o procurador
			matrix[x-1][y-1] = "p"; // "p" - procurador - bloco que tem possibilidade de ter vizinhos homogêneos
			fim = false;
			aposentados=0;
			while(!fim){
				procuradores = 0;
				for (i=0;i<=3;i++){
					for (j=0;j<=7;j++){
						if(matrix[j][i]=="p"){ // se é um procurador
							// transforma vizinhos homogêneos em procuradores
							if((i>0) && (matrix[j][i-1]=="c")){ // cima
								matrix[j][i-1] = "p";
								procuradores++;
							}if((j<7) && (matrix[j+1][i]=="c")){ // direita
								matrix[j+1][i] = "p";
								procuradores++;
							}if((i<3) && (matrix[j][i+1]=="c")){ // baixo
								matrix[j][i+1] = "p";
								procuradores++;
							}if((j>0) && (matrix[j-1][i]=="c")){ // esquerda
								matrix[j-1][i] = "p";
								procuradores++;
							}
							// aposenta o procurados
							matrix[j][i]="j"; // "j" - já - já aposentado
							aposentados++;
						}
					}
				}
				if (procuradores==0){
					fim = true;
				}
			}

			if(aposentados>=3){
				for (i=1;i<=4;i++){
					for (j=1;j<=8;j++){ 
						if(matrix[j-1][i-1]=="j"){ // se é pra espocar
							// espoca
							setCor(lado,j,i,-1);
						}
					}
				}
			}
			// causa dano no adversário equivalente ao número de blocos espocados
			P[inv(vezPlayer)].perdeVida(aposentados);
			desSelecionaTodos(vezPlayer);
			drop(vezPlayer);
		}
	}

	//derruba os blocos que estão no ar

	function dropVertical(lado){
		for (i=4;i>=1;i--){
			for (j=1;j<=8;j++){ 
				if(getCor(lado,j,i)==-1){ // se não tem cor
					// procura o primeiro acima
					for (k=i-1;k>=1;k--){
						if(getCor(lado,j,k)!=-1){ // se o encontrado tem cor
							setCor(lado,j,i,getCor(lado,j,k)) // em questão pega cor do encontrado
							setCor(lado,j,k,-1);
							break;
						}
					}	
				}
			}
		}
	}

	function isColVazia(lado,x){
		for(k=1;k<=4;k++){
			if(getCor(lado,x,k) != -1){
				return false;
			}
		}	
		return true;
	}

	function temColVazia(lado){
		for (i=1;i<=8;i++){
			if(isColVazia(lado,i)){
				return true;
			}
		}
		return false;
	}

	// troca colunas
	function trocaCol(lado,x,x2){
		cores=new Array;
		for(k=1;k<=4;k++)cores[k]=getCor(lado,x,k);
		for(k=1;k<=4;k++)setCor(lado,x,k,getCor(lado,x2,k));
		for(k=1;k<=4;k++)setCor(lado,x2,k,cores[k]);
	}

	//derruba os blocos que estão no ar

	function dropHorizontal(lado){
		for(i=7;i>=1;i--){
			for(j=i;j<=7;j++){
				if(isColVazia(lado,j)){
					trocaCol(lado,j,j+1);
				}
			}
		}
	}

	function drop(lado){
		dropVertical(lado);
		dropHorizontal(lado);
	}

	function isColLivre(lado,x){
		if(getCor(lado,x,1) == -1){
			return true;
		}else{
			return false;
		}
	}

	function qtdColLivres(lado){
		aux=0;
		for (var i = 1; i <= 8; i++) {
			if(isColLivre(lado,i)){
				aux++;
			}
		};
		return aux;
	}

	function createBlock(lado,x,cor){
		if(isColLivre(lado,x)){
			setCor(lado,x,1,cor);
			return true;
		}else{
			return false;
		}
	}

	function createRandBlocks(lado,qtd){
		for (var i = 1; i <=qtd; i++) {
			if(qtdColLivres(lado)>0){
				find = false;
				while(!find){
					col = Math.floor(Math.random()*8+1);
					if(isColLivre(lado,col)){
						cor = Math.floor(Math.random()*4);
						createBlock(lado,col,cor);
						find= true;
					}
				}
			}else{
				break;
			}
			drop(lado);
		};
	}

	function trocaVez(){
		if(vezPlayer==0)return false; // se acabou o jogo, não faz nada
		if(vezPlayer==1){
			vezPlayer = 2;
		}else{
			vezPlayer = 1;
		}
		$("#campo1").toggleClass("campoVez campoNVez");
		$("#campo2").toggleClass("campoVez campoNVez");
		createRandBlocks(vezPlayer,2);
	}

	function fimDeJogo(){
		vezPlayer = 0;
	}



	// FUNÇÕES DE ALTERAÇÃO

	// muda a classe de cor de um bloco
	// a partir de suas coordenadas
	function setCor(lado,x,y,cor){
		id=getBlocoID(lado,x,y);
		$(id).toggleClass("b-azul b-verm b-verd b-amar b-nada",false);
		$(id).toggleClass(getCorClass(cor),true);
	}

	function trocaSelecao(lado,x,y){
		bloco=$(getBlocoID(lado,x,y));
		bloco.toggleClass("selecionado desselecionado");
	}

	function seleciona(lado,x,y){
		bloco=$(getBlocoID(lado,x,y));
		if(bloco.hasClass("desselecionado")){
			trocaSelecao(lado,x,y);
		}
		
	}

	function desSeleciona(lado,x,y){
		bloco=$(getBlocoID(lado,x,y));
		if(bloco.hasClass("selecionado")){
			trocaSelecao(lado,x,y);
		}
		
	}

	function desSelecionaTodos(lado){
		for (i=1;i<=4;i++){
			for (j=1;j<=8;j++){
				desSeleciona(lado,j,i);
			}
		}
	}

	// EVENTOS

	// quando clica em um quadrado
	$(".bloco").click(function(){
		lado = getJObjLado($(this));
		x = getJObjX($(this));
		y = getJObjY($(this));
		if(getJObjLado($(this))==vezPlayer){
			if(estaSelecionado(lado,x,y)&&nivelGrupo(lado,x,y)>=3){
				espoca(lado,x,y);
				trocaVez();
			}else{
				if(!haBlocoSelecionado(lado)){ // se não há bloco selecionado, seleciona
					seleciona(lado,x,y);
				}else{
					//se clicou em vizinho selecionado heterogêneo, troca cores
					if(
						(isVizinho(x,y,getXSel(lado),getYSel(lado)))
						&&
						(getCor(lado,x,y)!=getCor(lado,getXSel(lado),getYSel(lado)))
						&&
						!(
							isVizinhoVert(x,y,getXSel(lado),getYSel(lado))
							&&(
								(getCor(lado,x,y)==-1)
								||
								(getCor(lado,getXSel(lado),getYSel(lado))==-1)
							)
						)
					)
						{ 						
						corAux = getCor(lado,x,y);
						setCor(lado,x,y,getCor(lado,getXSel(lado),getYSel(lado))); // atual recebe a cor do selecionado
						setCor(lado,getXSel(lado),getYSel(lado),corAux);
						desSelecionaTodos(lado);
						drop(lado);
						trocaVez();
					//se nao é vizinho ou é vizinho homogêneo, torna selecionado
					}else{
						desSelecionaTodos(lado);
						seleciona(lado,x,y);
					}
				}
			}
		}
	});

</script>
</html>
#  Projeto de Gestão de Certificados VPN

Este projeto implementa uma infraestrutura segura de acesso remoto via VPN, permitindo que cada funcionário tenha seu próprio certificado individual, com gestão descentralizada através de um painel web. A solução visa substituir o uso de certificados únicos compartilhados, promovendo segurança, autonomia e controle de acessos.

---

## Máquinas e Estrutura Utilizadas no Projeto

###  OpenvpnServer

**Função:** Servidor principal da VPN e do painel web

**Serviços instalados:**
- **OpenVPN + Easy-RSA:** Geração e gerenciamento de certificados de VPN individuais.
- **Apache + PHP:** Hospedagem do painel web onde usuários podem gerar/revogar certificados e redefinir senhas.
- **Scripts shell personalizados:** Automatizam a criação (`criar_certificado.sh`) e revogação (`revogar_certificado.sh`) de certificados.

**Resumo:** Esta maquina possuí um adaptador de rede, que para ser configurada inicialmente, é recomendado que ela esteja em "Bridge Mode", para que ela se conecte com rede externa para ser configurada. Após isso ela devera fncionar em modo de "Rede Interna" pois o Firewall estabelecerá a conexão para a mesma, pois a OpenVpn é o servidor central do sistema, unificando os serviços de rede, autenticação, VPN e interface.

---

### Firewall

**Função:** Controlar e proteger o tráfego de rede

**Ferramenta utilizada:** `nftables`

| Componente  | Descrição                                                                 |
|-------------|---------------------------------------------------------------------------|
| **nftables**| Controla o tráfego de entrada/saída, permitindo apenas serviços essenciais |
| **Política padrão** | Bloquear tudo, exceto:                                              |
|  **Porta 80/TCP**| Para redirecionamento HTTP → HTTPS                                       |
| **Porta 443/TCP**| Interface web segura                                                     |
|  **Porta 1194/UDP** | Conexão dos clientes VPN                                               |
|  **Porta 22/TCP** | Permitido apenas para rede interna `192.168.200.0/24`       |

**Resumo:** Essa máquina atua como barreira entre a internet e o OpenvpnServer.

---

### Database

**Função:** Armazenar dados da aplicação, sobre o que se ocorre no OpenVPN, como de exemplo, gerar um novo certificado.

**Serviço instalado:** MariaDB

**Tabelas principais:**
- `usuarios`: Armazena dados de login, senhas (hash) e status de atividade.
- `certificados`: Registra os certificados gerados por cada usuário, com identificadores únicos e datas.

**Resumo:** Armazena todas as informações críticas de forma segura e integrada ao painel web.

---
# Escopo do Projeto 
O projeto consiste na construção de um ambiente seguro utilizando VPN, com foco na gestão individual de certificados de autenticação por meio de um painel web funcional e seguro.

A proposta é descentralizar essa gestão, permitindo que cada funcionário gere e revogue seus próprios certificados, sem intervenção constante do administrador de rede. 

## Estrutura do Ambiente
O sistema é composto por três máquinas Debian 12, cada uma com uma função bem definida:

Firewall: controla e filtra o tráfego de entrada e saída da rede.

OpenVPNServer: servidor que hospeda a VPN, o painel web e os scripts de certificação.

Database: responsável por armazenar os dados de usuários, certificados e tokens de redefinição de senha.

## Configuração de Rede
Todas as máquinas usam interfaces configuradas manualmente no arquivo /etc/network/interfaces. Abaixo, segue a descrição de cada uma:

### Firewall
Essa máquina atua como fronteira entre a internet e os serviços internos (painel e VPN). Possui duas interfaces: uma externa (com DHCP) e uma interna (com IP fixo), permitindo aplicar NAT e regras de controle. Entrando na interface utilizando **sudo nano /etc/network/interfaces**.

Configuração:

```bash
auto lo
iface lo inet loopback
source /etc/network/interfaces.d/*
auto enp0s8
iface enp0s8 inet dhcp
auto enp0s3
iface enp0s3 inet static
    address 10.0.0.1
    netmask 255.255.255.0
    network 10.0.0.0
    broadcast 10.0.0.255
```



### OpenVPNServer
Essa máquina oferece os serviços de VPN (via OpenVPN) e o painel web de gestão de certificados. Também armazena os scripts para criação e revogação dos certificados. Entrando na interface utilizando **sudo nano /etc/network/interfaces**.

Configuração:
```bash
source /etc/network/interfaces.d/*
auto lo
iface lo inet loopback
auto enp0s3
iface enp0s3 inet static
    address 10.0.0.10
    netmask 255.255.255.0
    network 10.0.0.0
    broadcast 10.0.0.255
    gateway 10.0.0.1


```
obs: pode ser usado para facilitar o desenvolvimento, a seguinte Configuração:
```bash
auto enp0s3
iface inet dhcp
```
ela fara com que a OpenVPN possa se conectar, caso a vm Firewall ainda não esja feita. Mas a Configuração anterior deve ser comentada e alterar o adaptador para BRIDGE, em seguuida reiniciada.

### DatabaseServer 
Responsável por armazenar de forma segura todas as informações da aplicação, como dados de login dos usuários, histórico de certificados gerados. Entrando na interface utilizando **sudo nano /etc/network/interfaces**.

Configuração:

```bash
source /etc/network/interfaces.d/*
auto lo
iface lo inet loopback
auto enp0s3
iface enp0s3 inet static
    address 10.0.0.20
    netmask 255.255.255.0
    network 10.0.0.0
    broadcast 10.0.0.255
    gateway 10.0.0.1
```

# Instalando o Ambiente 

Esta etapa descreve como configurar o ambiente completo do projeto de VPN com painel de gestão de certificados, utilizando três máquinas Debian 12: `OpenvpnServer`, `Firewall` e `Database`.

---

## Máquinas Utilizadas

| VM           | Função                        | Serviços Instalados                         |
|--------------|-------------------------------|---------------------------------------------|
| OpenvpnServer| Servidor VPN + Painel Web     | OpenVPN, Apache, PHP      |
| Firewall     | Proteção de rede              | nftables                                    |
| Database     | Banco de dados                | MariaDB                                     |

---

### Instalação e Configuração por Máquina

---

## OpenvpnServer

#### 1. Atualize e instale os pacotes:
```bash
sudo apt update && sudo apt install openvpn easy-rsa apache2 php libapache2-mod-php php-mysql 
```

#### 2. Configure a OpenVPN:
```bash
gunzip -c /usr/share/doc/openvpn/examples/sample-config-files/server.conf.gz | sudo tee /etc/openvpn/server.conf
```

#### 3. Configurar Easy-rsa:
```bash
make-cadir ~/openvpn-ca
cd ~/openvpn-ca
./easyrsa init-pki
./easyrsa build-ca
./easyrsa gen-req server nopass
./easyrsa sign-req server server
./easyrsa gen-dh
./easyrsa gen-crl
```

#### 4. Ativando o IP forwarding:
```bash
echo "net.ipv4.ip_forward=1" | sudo tee -a /etc/sysctl.conf
sudo sysctl -p
```

#### 5. Iniciando o Serviço:
```bash
sudo systemctl enable openvpn-server@server
sudo systemctl start openvpn-server@server
```

#### 6.Instalar e configurar o painel web:
Copie os arquivos .php para /var/www/html 

Reinicie o Apache:
```bash
sudo systemctl restart apache2
```

---

## Firewall
### Portas Necessárias no Firewall

| Porta | Protocolo | Serviço        | Descrição                                                      |
|-------|-----------|----------------|----------------------------------------------------------------|
| 80    | TCP       | Container WEB  | Permitir redirecionamento HTTP para HTTPS (porta 443)         |
| 443   | TCP       | Container WEB  | Interface web acessada pelo navegador (HTTPS)                 |
| 1194  | UDP       | OpenVPN        | Porta do serviço VPN usada pelos clientes para se conectar    |

#### 1. Instalar as nftables:
```bash
sudo apt install nftables -y
```
#### 2. Criando as regras NFT:
Para criar as regras nft acesse o arquivo: 
```bash
sudo nano /etc/nftables.conf
```
em sequida use o comando:
```bash
sudo nft -f /etc/nftables.conf 
sudo sh -c 'nft list ruleset > /etc/nftables.conf'

sudo systemctl enable nftables
```

---

## Database

#### 1. Instalando o MariaDB:
Use o comando:
```bash
sudo apt install mariadb-server -y
```
```bash
sudo mysql_secure_installation
```

#### 2. Acessando o MariaDB:
```bash
sudo mysql -u root -p
```
Em seguida, você podera criar sua DATABASE, Tables, etc.

Para sair da area do MariaDB, use: 
```bash
EXIT;
```

### Cuidados e Atenções

Coloque os scripts em /usr/local/bin/:

- criar_certificado.sh: Gera certificado e cria arquivo .zip.

- revogar_certificado.sh: Revoga e remove o certificado.

- Ambos devem ter permissão de execução:

```bash
chmod +x /usr/local/bin/criar_certificado.sh
chmod +x /usr/local/bin/revogar_certificado.sh
```


## conectando no docker garantia_app

sudo docker exec -it garantia_app bash

## Entre na pasta do frontend:

cd /root/garantia/frontend

## Garantir que o Git local está configurado para o repositório correto:

git init
git remote add origin https://github.com/geraldopatricio/BrotherMotos-SistemaGarantia-Frontend.git

# Forçar a atualização dos arquivos:

git fetch --all
git reset --hard origin/main

# Deploy para o Container Docker

## Copie os arquivos do host para dentro do container:

docker cp /root/garantia/frontend/. garantia_app:/app/frontend/

### Reinicie o processo interno (Opcional):

docker restart garantia_app

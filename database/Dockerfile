FROM postgres:10

# https://medium.com/coding-blocks/creating-user-database-and-adding-access-on-postgresql-8bfcd2f4a91e

COPY init-movies.sh /docker-entrypoint-initdb.d/init-user-db.sh

EXPOSE 5432

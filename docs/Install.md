
Setting permissions:

```sh
sudo addgroup cetraria
sudo usermod -a -G cetraria www-data
sudo chmod g+w var/{logs,pids} var/cache/{annotations,data,metadata,volt}
```

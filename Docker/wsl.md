wsl --install debian

alpine
debian

exit

wsl --manage alpine --set-default-user root
wsl --manage debian --set-default-user root

wsl --terminate alpine
wsl --terminate debian

wsl --unregister alpine
wsl --unregister debian

wsl --shutdown
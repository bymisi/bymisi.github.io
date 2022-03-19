@echo off
    IF EXIST x86.w7 (
        echo Previous Setup Files are Windows 7 x86
        GOTO done
    ) ELSE (
        echo Finding Previous Setup Files...
    )
    IF EXIST x64.w7 (
        echo Previous Setup Files are Windows 7 x64
	ren boot 64boot
	ren efi 64efi
	ren sources 64sources
	ren support 64support
	ren upgrade 64upgrade 
	ren uui 64uui
	ren bootmgr 64bootmgr
	ren chain.c32 64chain.c32
	ren bootmgr.efi 64bootmgr.efi
	ren setup.exe 64setup.exe
	ren license.txt 64license.txt
	ren Uni-USB-Installer-Copying.txt 64Uni-USB-Installer-Copying.txt 
	ren Uni-USB-Installer-Readme.txt 64Uni-USB-Installer-Readme.txt
	ren menu.lst 64menu.lst

	ren 86boot boot
	ren 86efi efi
	ren 86multiboot multiboot
	ren 86support support
	ren 86upgrade upgrade
	ren 86.disk .disk
	ren 86bootmgr bootmgr
	ren 86setup.exe setup.exe
	ren 86sources sources
	ren 86menu.lst menu.lst
	ren x64.w7 x86.w7
        GOTO done
    ) ELSE (
        echo.
    )
GOTO error
:done
echo DONE
pause
GOTO END
:error
echo FILE IS MiSSiNG!!!
pause
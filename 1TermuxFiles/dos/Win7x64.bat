@echo off
    IF EXIST x64.w7 (
        echo Previous Setup Files are Windows 7 x64
        GOTO done
    ) ELSE (
        echo Finding Previous Setup Files...
    )
    IF EXIST x86.w7 (
        echo Previous Setup Files are Windows 7 x86
	ren boot 86boot
	ren efi 86efi
	ren multiboot 86multiboot
	ren support 86support
	ren upgrade 86upgrade
	ren .disk 86.disk
	ren bootmgr 86bootmgr
	ren setup.exe 86setup.exe
	ren sources 86sources
	ren menu.lst 86menu.lst

	ren 64boot boot
	ren 64efi efi
	ren 64sources sources
	ren 64support support
	ren 64upgrade upgrade 
	ren 64uui uui
	ren 64bootmgr bootmgr
	ren 64chain.c32 chain.c32
	ren 64bootmgr.efi bootmgr.efi
	ren 64setup.exe setup.exe
	ren 64license.txt license.txt
	ren 64Uni-USB-Installer-Copying.txt Uni-USB-Installer-Copying.txt 
	ren 64Uni-USB-Installer-Readme.txt Uni-USB-Installer-Readme.txt
	ren 64menu.lst menu.lst
	ren x86.w7 x64.w7
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
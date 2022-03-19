@echo off
    IF EXIST 2010.XPSETUP (
        echo Previous Setup Files are 2010
        GOTO done
    ) ELSE (
        echo Finding Previous Setup Files...
    )
    IF EXIST HUN.XPSETUP (
        echo Previous Setup Files are HUN
	ren $WIN_NT$.~LS HUN$WIN_NT$.~LS
	ren $WIN_NT$.~BT HUN$WIN_NT$.~BT
	ren TXTSETUP.SIF HUNTXTSETUP.SIF
	ren NTLDR HUNNTLDR
	ren XATSP HUNXATSP 
	ren XPSTP HUNXPSTP
	ren menu.lst HUNmenu.lst

	ren 2010$WIN_NT$.~LS $WIN_NT$.~LS
	ren 2010$WIN_NT$.~BT $WIN_NT$.~BT
	ren 2010txtsetup.sif txtsetup.sif
	ren 2010NTLDR NTLDR
	ren DEFXATSP XATSP 
	ren DEFXPSTP XPSTP
	ren 2010menu.lst menu.lst
	ren *.XPSETUP 2010.XPSETUP
        GOTO done
    ) ELSE (
        echo.
    )
    IF EXIST DEF.XPSETUP (
        echo Previous Setup Files are DEF
	ren $WIN_NT$.~LS DEF$WIN_NT$.~LS
	ren $WIN_NT$.~BT DEF$WIN_NT$.~BT
	ren TXTSETUP.SIF DEFTXTSETUP.SIF
	ren NTLDR DEFNTLDR
	ren menu.lst DEFmenu.lst

	ren 2010$WIN_NT$.~LS $WIN_NT$.~LS
	ren 2010$WIN_NT$.~BT $WIN_NT$.~BT
	ren 2010txtsetup.sif txtsetup.sif
	ren 2010NTLDR NTLDR
	ren 2010menu.lst menu.lst
	ren *.XPSETUP 2010.XPSETUP
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
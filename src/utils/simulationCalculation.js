export function simulationCalculation(meatData) {
  const {
    carcaca,
    alcatra,
    patinho,
    coxaoFora,
    tatu,
    coxaoMole,
    picanha,
    maminha,
    fileMignon,
    contraFile,
    file,
    paleta,
    agulha,
    pontaPeito,
    guisado,
    matambre,
    musculo,
    vazio,
    costela,
    costelaMinga,
    pescoco,
    ossoCebo,
  } = meatData;

  const Q = 22; // Definido como um valor fixo
  const pb = 1;

  const pAlcatra = (((18 - Q) * 3) / 100) + 3;
  const pPatinho = (((18 - Q) * 3.7) / 100) + 3.7;
  const pCoxaoF = (((18 - Q) * 4) / 100) + 4;
  const pCoxaoM = (((18 - Q) * 6) / 100) + 6;
  const pTatu = (((18 - Q) * 1.7) / 100) + 1.7;
  const pPicanha = (((18 - Q) * 1.5) / 100) + 1.5;
  const pMaminha = (((18 - Q) * 1.2) / 100) + 1.2;
  const pFileM = (((18 - Q) * 1.2) / 100) + 1.2;
  const pContraF = (((18 - Q) * 3) / 100) + 3;
  const pFile = (((18 - Q) * 9) / 100) + 9;
  const pPaleta = (((18 - Q) * 7.5) / 100) + 7.5;
  const pAgulha = (((18 - Q) * 6.8) / 100) + 6.8;
  const pPontaP = (((18 - Q) * 4) / 100) + 4;
  const pGuisado = (((18 - Q) * 7.5) / 100) + 7.5;
  const pOsso = Q;
  const pVazio = (((18 - Q) * 3.8) / 100) + 3.8;
  const pCostela = (((18 - Q) * 5.5) / 100) + 5.5;
  const pCostelaM = (((18 - Q) * 2.2) / 100) + 2.2;
  const pMatambre = (((18 - Q) * 1.6) / 100) + 1.6;
  const pPescoço = (((18 - Q) * 4) / 100) + 4;
  const pMusculo = (((18 - Q) * 4.8) / 100) + 4.8;

  const pFinalAlcatra = ((pb * pAlcatra) / 100) * alcatra;
  const pFinalPatinho = ((pb * pPatinho) / 100) * patinho;
  const pFinalCoxaoF = ((pb * pCoxaoF) / 100) * coxaoFora;
  const pFinalCoxaoM = ((pb * pCoxaoM) / 100) * coxaoMole;
  const pFinalTatu = ((pb * pTatu) / 100) * tatu;
  const pFinalPicanha = ((pb * pPicanha) / 100) * picanha;
  const pFinalMaminha = ((pb * pMaminha) / 100) * maminha;
  const pFinalFileM = ((pb * pFileM) / 100) * fileMignon;
  const pFinalContraF = ((pb * pContraF) / 100) * contraFile;
  const pFinalFile = ((pb * pFile) / 100) * file;
  const pFinalPaleta = ((pb * pPaleta) / 100) * paleta;
  const pFinalAagulha = ((pb * pAgulha) / 100) * agulha;
  const pFinalPontaP = ((pb * pPontaP) / 100) * pontaPeito;
  const pFinalGuisado = ((pb * pGuisado) / 100) * guisado;
  const pFinalOsso = ((pb * pOsso) / 100) * ossoCebo;
  const pFinalVazio = ((pb * pVazio) / 100) * vazio;
  const pFinalCostela = ((pb * pCostela) / 100) * costela;
  const pFinalCostelaM = ((pb * pCostelaM) / 100) * costelaMinga;
  const pFinalMatambre = ((pb * pMatambre) / 100) * matambre;
  const pFinalPescoço = ((pb * pPescoço) / 100) * pescoco;
  const pFinalMusculo = ((pb * pMusculo) / 100) * musculo;

  const custoBoi = carcaca * pb;

  const preçoVenda = pFinalAlcatra + pFinalPatinho + pFinalCoxaoF + pFinalCoxaoM + pFinalTatu
        + pFinalPicanha + pFinalMaminha + pFinalFileM + pFinalContraF + pFinalFile + pFinalPaleta
        + pFinalAagulha + pFinalPontaP + pFinalGuisado + pFinalOsso + pFinalVazio + pFinalCostela
        + pFinalCostelaM + pFinalMatambre + pFinalPescoço + pFinalMusculo;

  const lucroBruto = preçoVenda.toFixed(2);
  const perdaEmOsso = ((pb * pOsso) / 100).toFixed(2);
  const lucroLiquido = (preçoVenda - custoBoi).toFixed(2);
  const lucroMedioKg = ((preçoVenda - custoBoi) / pb).toFixed(2);

  if (lucroBruto && perdaEmOsso && lucroLiquido && lucroMedioKg) {
    return {
      lucroBruto, perdaEmOsso, lucroLiquido, lucroMedioKg,
    };
  }
  throw new Error('calc-error');
}
